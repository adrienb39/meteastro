<style>
    /* ==========================================
   CHARTE AVVA39 - CARTOGUIDE PRO (HAUTE LISIBILITÉ)
   ========================================== */
    :root {
        --avva-blue: #0070f3;
        --avva-blue-light: #38bdf8;
        --avva-green: #10b981;
        --avva-dark-bg: #090d16;
        --avva-card-bg: #151d2a;
        --avva-modal-bg: #1e293b;
        --avva-text-main: #ffffff;
        --avva-text-muted: #cbd5e1;
        --avva-border: #334155;
        --avva-accent: #ef4444;
        --avva-radius-card: 16px;
        --avva-radius-elem: 10px;
    }

    /* En-tête Principal Haute Visibilité */
    .avva-header-main {
        position: relative;
        background: var(--avva-card-bg);
        border: 2px solid var(--avva-border);
        border-radius: var(--avva-radius-card);
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .avva-header-top-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--avva-blue), var(--avva-green));
    }

    /* Titres Ultra Lisibles */
    .avva-title {
        color: var(--avva-blue-light);
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900;
        letter-spacing: 0.03em;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        margin: 0;
    }

    .avva-slogan {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--avva-text-main);
    }

    .avva-logo {
        width: 38px;
        height: auto;
        filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.5));
    }

    /* Badges d'Action Haute Lisibilité */
    .avva-action-badges {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .avva-badge-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #ffffff !important;
        background: #1e293b;
        border: 1px solid #475569;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .avva-badge-btn:hover {
        background: var(--avva-blue);
        border-color: var(--avva-blue-light);
    }

    .avva-badge-news {
        background: #881337;
        border-color: #f43f5e;
    }

    .avva-badge-news:hover {
        background: #e11d48;
    }

    /* Barre d'Outils Professionnelle Cartoguide */
    .avva-pro-toolbar {
        background: #0f172a;
        border: 1px solid var(--avva-border);
        border-radius: var(--avva-radius-elem);
        padding: 0.75rem 1rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .avva-toolbar-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .avva-tool-btn {
        background: #1e293b;
        color: var(--avva-text-main);
        border: 1px solid var(--avva-border);
        padding: 0.45rem 0.85rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .avva-tool-btn:hover {
        background: var(--avva-blue);
        color: #ffffff;
    }

    /* Styles des Modals & Offcanvas */
    .avva-modal-custom .modal-content,
    .avva-offcanvas {
        background: var(--avva-modal-bg);
        color: var(--avva-text-main);
        border-radius: var(--avva-radius-card);
        border: 2px solid var(--avva-border);
    }

    .avva-offcanvas {
        border-radius: 0;
        border-left: 2px solid var(--avva-border);
    }

    .avva-modal-custom .modal-header,
    .avva-offcanvas .offcanvas-header {
        border-bottom: 1px solid var(--avva-border);
    }

    .avva-modal-custom .modal-footer {
        border-top: 1px solid var(--avva-border);
    }

    .avva-info-item {
        background: #0f172a;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--avva-border);
        margin-bottom: 0.75rem;
    }

    .avva-info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--avva-text-muted);
        font-weight: 800;
        letter-spacing: 0.05em;
    }

    .avva-info-val {
        font-size: 1rem;
        color: #ffffff;
        font-weight: 700;
    }

    .avva-danger-box {
        background: #2c0b0e;
        border: 1px solid #f43f5e;
        border-radius: var(--avva-radius-elem);
        padding: 1.25rem;
        margin-top: 1.25rem;
    }

    /* Éléments de la liste GPX */
    .gpx-card-item {
        background: #0f172a;
        border: 1px solid var(--avva-border);
        border-radius: var(--avva-radius-elem);
        padding: 0.85rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .gpx-card-item:hover,
    .gpx-card-item.active {
        border-color: var(--avva-blue-light);
        background: #152238;
    }

    /* Zone Carte / Iframe */
    .app-iframe-area {
        width: 100%;
        height: calc(100vh - 320px);
        min-height: 550px;
        border-radius: var(--avva-radius-card);
        overflow: hidden;
        border: 2px solid var(--avva-border);
        background: #000000;
    }

    /* Notification Toast Copie */
    .toast-share-copy {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        background: var(--avva-green);
        color: #fff;
        padding: 0.75rem 1.25rem;
        border-radius: var(--avva-radius-elem);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        font-weight: 700;
        display: none;
    }

    /* ==========================================
   MODALS & OFFCANVAS MODERNES (GLASSMORPHISM & AVVA39)
   ========================================== */

    /* --- 1. Animation d'apparition fluide --- */
    @keyframes modalFloatIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Back-drop flouté modernisé (Arrière-plan) */
    .modal-backdrop.show {
        backdrop-filter: blur(8px);
        background-color: rgba(9, 13, 22, 0.75);
    }

    /* --- 2. Modals Modernes Flottantes --- */
    .avva-modal-custom .modal-dialog {
        margin: 1.75rem auto;
    }

    .avva-modal-custom .modal-content {
        /* Effet Glassmorphism (Verre dépoli) */
        background: rgba(30, 41, 59, 0.85) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);

        color: var(--avva-text-main);
        border-radius: 24px !important;

        /* Bordure fine en dégradé subtil */
        border: 1px solid rgba(255, 255, 255, 0.1) !important;

        /* Double ombre portée pour effet de profondeur moderne */
        box-shadow:
            0 20px 50px rgba(0, 0, 0, 0.6),
            0 0 30px rgba(0, 112, 243, 0.2) !important;

        overflow: hidden;
        animation: modalFloatIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* En-tête de Modal Moderne */
    .avva-modal-custom .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.25rem 1.5rem;
        position: relative;
        background: linear-gradient(135deg, rgba(0, 112, 243, 0.12), rgba(16, 185, 129, 0.12));
    }

    /* Ligne néon colorée sous l'en-tête */
    .avva-modal-custom .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 10%;
        width: 80%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--avva-blue-light), var(--avva-green), transparent);
    }

    /* --- 3. Offcanvas (Menu Latéral) Moderne & Flottant --- */
    .avva-offcanvas {
        background: rgba(30, 41, 59, 0.9) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        color: var(--avva-text-main);

        /* Positionnement Flottant */
        top: 16px !important;
        right: 16px !important;
        bottom: 16px !important;
        height: calc(100vh - 32px) !important;

        /* Forme & Ombres */
        border-radius: 24px !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow:
            -10px 20px 40px rgba(0, 0, 0, 0.5),
            0 0 25px rgba(16, 185, 129, 0.15) !important;
    }

    .avva-offcanvas.offcanvas-start {
        left: 16px !important;
        right: auto !important;
    }

    .avva-offcanvas .offcanvas-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(0, 112, 243, 0.12));
    }

    /* --- 4. Composants Intérieurs Modernisés --- */
    /* Titres avec dégradé de couleur */
    .avva-modal-custom .modal-title,
    .avva-offcanvas .offcanvas-title {
        font-weight: 800 !important;
        letter-spacing: 0.02em;
        background: linear-gradient(90deg, #ffffff, var(--avva-blue-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Cartes d'informations / éléments de liste */
    .avva-info-item,
    .gpx-card-item {
        background: rgba(15, 23, 42, 0.6) !important;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 16px !important;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Effet Hover dynamique sur les cartes GPX */
    .gpx-card-item:hover,
    .gpx-card-item.active {
        transform: translateY(-2px);
        border-color: var(--avva-blue-light) !important;
        background: rgba(0, 112, 243, 0.12) !important;
        box-shadow: 0 8px 20px rgba(0, 112, 243, 0.2);
    }

    /* Boutons de fermeture (Croix) modernisés */
    .btn-close-white {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .btn-close-white:hover {
        transform: rotate(90deg) scale(1.1);
        opacity: 1;
    }

    /* ==========================================
   OPTIONS DU PARCOURS SÉLECTIONNÉ
   ========================================== */

    /* Carte GPX sélectionnée (État actif) */
    .gpx-card-item.active {
        border-color: var(--avva-blue-light) !important;
        background: linear-gradient(135deg, rgba(0, 112, 243, 0.15), rgba(16, 185, 129, 0.1)) !important;
        box-shadow: 0 0 20px rgba(0, 112, 243, 0.25);
    }

    /* Panneau d'actions qui s'affiche à la sélection */
    .gpx-selected-actions {
        display: none;
        /* Masqué par défaut */
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px dashed rgba(255, 255, 255, 0.15);
        animation: fadeInOptions 0.25s ease-in-out forwards;
    }

    .gpx-card-item.active .gpx-selected-actions {
        display: block;
        /* Visible uniquement sur le parcours actif */
    }

    /* Grille de boutons d'action */
    .gpx-action-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .gpx-action-btn {
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }

    .gpx-action-btn:hover {
        transform: translateY(-1px);
    }

    @keyframes fadeInOptions {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* --- VARIABLES DE THÈME METEOR/DARK --- */
    :root {
        --bg-glass: rgba(18, 24, 38, 0.85);
        --border-glass: rgba(255, 255, 255, 0.12);
        --cyan-accent: #00f2fe;
        --blue-accent: #4facfe;
        --amber-accent: #ffb300;
        --text-muted: #8a99ad;
    }

    /* --- GLASS CARDS & MODAL --- */
    .glass-card {
        background: var(--bg-glass) !important;
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid var(--border-glass) !important;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), 0 0 40px rgba(0, 242, 254, 0.05);
    }

    .text-gradient {
        background: linear-gradient(135deg, #ffffff 0%, #00f2fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .text-cyan {
        color: var(--cyan-accent);
    }

    /* Badge Icône Header */
    .header-icon-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(0, 242, 254, 0.15), rgba(79, 172, 254, 0.05));
        border: 1px solid rgba(0, 242, 254, 0.3);
        color: var(--cyan-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    /* --- TITRES DE SECTION --- */
    .section-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
    }

    /* --- MODERN INPUTS --- */
    .modern-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .modern-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #d1d5db;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        color: var(--text-muted);
        font-size: 1.1rem;
        transition: color 0.3s;
    }

    .textarea-icon {
        top: 14px;
    }

    .modern-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #fff;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modern-input:focus {
        background: rgba(255, 255, 255, 0.07);
        border-color: var(--cyan-accent);
        box-shadow: 0 0 15px rgba(0, 242, 254, 0.25);
    }

    .modern-input:focus+.input-icon,
    .modern-input:focus~.input-icon {
        color: var(--cyan-accent);
    }

    /* --- SÉPARATEUR LUMINEUX --- */
    .glow-separator {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    }

    /* --- SÉLECTEUR DE VISIBILITÉ (Segmented Control) --- */
    .segmented-control-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .segmented-option input[type="radio"] {
        display: none;
    }

    .option-content {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .option-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: rgba(0, 0, 0, 0.3);
        color: var(--text-muted);
        transition: all 0.3s ease;
    }

    .option-title {
        display: block;
        font-weight: 700;
        font-size: 0.95rem;
        color: #fff;
    }

    .option-sub {
        display: block;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Interactions Hover & Selected */
    .segmented-option:hover .option-content {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Privatisation sélectionnée */
    .segmented-option input[value="0"]:checked+.option-content {
        background: rgba(255, 179, 0, 0.08);
        border-color: var(--amber-accent);
        box-shadow: 0 0 20px rgba(255, 179, 0, 0.2);
    }

    .segmented-option input[value="0"]:checked+.option-content .option-icon {
        background: var(--amber-accent);
        color: #000;
    }

    /* Public sélectionné */
    .segmented-option input[value="1"]:checked+.option-content {
        background: rgba(0, 242, 254, 0.08);
        border-color: var(--cyan-accent);
        box-shadow: 0 0 20px rgba(0, 242, 254, 0.2);
    }

    .segmented-option input[value="1"]:checked+.option-content .option-icon {
        background: var(--cyan-accent);
        color: #000;
    }

    /* --- BOUTONS ULTRA-MODERNES --- */
    .btn-ultra {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-primary-glow {
        background: linear-gradient(135deg, var(--blue-accent), var(--cyan-accent));
        color: #000;
        box-shadow: 0 4px 15px rgba(0, 242, 254, 0.3);
    }

    .btn-primary-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 242, 254, 0.5);
    }

    .btn-amber-glow {
        background: linear-gradient(135deg, #ffc107, var(--amber-accent));
        color: #000;
        box-shadow: 0 4px 15px rgba(255, 179, 0, 0.3);
    }

    .btn-amber-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 179, 0, 0.5);
    }

    .btn-ghost {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    @media (max-width: 575.98px) {
        .avva-header-main {
            padding: 1rem;
        }

        .avva-action-badges {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .avva-action-badges .avva-badge-btn {
            width: 100%;
            justify-content: center;
        }

        .avva-action-badges>div.d-flex {
            width: 100%;
            gap: 0.5rem;
        }

        .avva-action-badges>div.d-flex .avva-badge-btn {
            flex: 1;
        }

        .avva-slogan {
            font-size: 0.95rem;
        }

        .avva-pro-toolbar {
            justify-content: center;
            padding: 0.6rem;
        }

        .avva-toolbar-group {
            justify-content: center;
            width: 100%;
        }

        .avva-tool-btn {
            flex: 1 1 calc(50% - 0.5rem);
            justify-content: center;
            font-size: 0.78rem;
            padding: 0.5rem 0.6rem;
            white-space: nowrap;
        }

        .avva-tool-btn span {
            display: none;
        }

        .avva-tool-btn::after {
            content: attr(data-label-mobile);
            font-size: 0.72rem;
        }

        #gpxAutoSaveState {
            width: 100%;
            text-align: center;
            margin-top: 0.5rem;
            padding: 0;
        }

        .app-iframe-area {
            height: calc(100vh - 260px);
            min-height: 380px;
        }

        .avva-offcanvas {
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            width: 100vw !important;
            max-width: 100vw !important;
            border-radius: 0 !important;
        }

        .avva-offcanvas.offcanvas-start {
            left: 0 !important;
        }

        .segmented-control-container {
            grid-template-columns: 1fr;
        }

        .option-content {
            padding: 12px;
        }

        .gpx-action-grid {
            grid-template-columns: 1fr;
        }

        .header-icon-badge {
            width: 38px;
            height: 38px;
            font-size: 1.1rem;
        }

        .avva-modal-custom-pro .modal-header {
            padding: 1rem;
        }

        .avva-modal-custom-pro .modal-body {
            padding: 1rem !important;
        }

        .cartoguide-floating-bar {
            z-index: 999 !important;
        }
    }

    .content-section-page-cartoguide {
        margin: 90px auto;
    }

    .toast-notification-container {
    position: fixed;
    top: 1.25rem;
    right: 1.25rem;
    z-index: 1080;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-width: 380px;
}

.toast-notif {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.9rem 2.2rem 0.9rem 1rem;
    border-radius: 0.75rem;
    background: #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    overflow: hidden;
    animation: toastSlideIn 0.35s cubic-bezier(0.21, 1.02, 0.73, 1) forwards;
}

.toast-notif.toast-notif-hide {
    animation: toastSlideOut 0.35s ease forwards;
}

.toast-notif-success {
    border-left: 4px solid #198754;
}
.toast-notif-success .toast-notif-icon { color: #198754; }

.toast-notif-info {
    border-left: 4px solid #0dcaf0;
}
.toast-notif-info .toast-notif-icon { color: #0dcaf0; }

.toast-notif-icon {
    font-size: 1.3rem;
    line-height: 1;
    margin-top: 0.1rem;
}

.toast-notif-content {
    display: flex;
    flex-direction: column;
    font-size: 0.9rem;
}

.toast-notif-content strong {
    margin-bottom: 0.15rem;
}

.toast-notif-close {
    position: absolute;
    top: 0.6rem;
    right: 0.7rem;
    background: none;
    border: none;
    color: #6c757d;
    font-size: 0.8rem;
    cursor: pointer;
}

.toast-notif-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 100%;
    background: rgba(0, 0, 0, 0.1);
}

.toast-notif-progress::after {
    content: "";
    display: block;
    height: 100%;
    width: 100%;
    background: currentColor;
    transform-origin: left;
    animation: toastProgress linear forwards;
    animation-duration: inherit;
}

.toast-notif-success .toast-notif-progress::after { background: #198754; }
.toast-notif-info .toast-notif-progress::after { background: #0dcaf0; }

@keyframes toastSlideIn {
    from { opacity: 0; transform: translateX(110%); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes toastSlideOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(110%); }
}

@keyframes toastProgress {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}
</style>

<div id="toastShare" class="toast-share-copy align-items-center">
    <i class="bi bi-check-circle-fill me-2"></i> Lien du parcours copié dans le presse-papier !
</div>

<div id="global-loader-overlay" class="loader-global d-flex justify-content-center align-items-center">
    <div class="loader-content text-center">
        <img src="/assets/images/logo-avva39.png" alt="Chargement AVVA39" class="loader-logo-pulsating mb-3">
    </div>
</div>

<section class="container-fluid content-section-page-cartoguide py-3" id="contenu-page">

    <div class="toast-notification-container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="toast-notif toast-notif-success" role="alert" data-autohide="5000">
            <div class="toast-notif-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="toast-notif-content">
                <strong>Succès</strong>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <button type="button" class="toast-notif-close" aria-label="Fermer">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="toast-notif-progress"></div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['login_success_once'])): ?>
        <div class="toast-notif toast-notif-info" role="alert" data-autohide="5000">
            <div class="toast-notif-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="toast-notif-content">
                <strong>Bienvenue</strong>
                <span><?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?> ! Votre session sécurisée est active.</span>
            </div>
            <button type="button" class="toast-notif-close" aria-label="Fermer">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="toast-notif-progress"></div>
        </div>
        <?php unset($_SESSION['login_success_once']); ?>
    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toast-notif').forEach(function (toast) {
        const delay = parseInt(toast.dataset.autohide, 10) || 5000;
        const progress = toast.querySelector('.toast-notif-progress::after');
        toast.style.setProperty('--toast-duration', delay + 'ms');
        toast.querySelector('.toast-notif-progress').style.setProperty('animation-duration', delay + 'ms');

        const closeToast = () => {
            toast.classList.add('toast-notif-hide');
            toast.addEventListener('animationend', () => toast.remove(), { once: true });
        };

        const timer = setTimeout(closeToast, delay);

        toast.querySelector('.toast-notif-close').addEventListener('click', function () {
            clearTimeout(timer);
            closeToast();
        });
    });
});
</script>

    <header class="avva-header-main mb-3 position-relative">
        <div class="avva-header-top-bar"></div>

        <div class="avva-action-badges">
            <button class="avva-badge-btn text-truncate" type="button" data-bs-toggle="modal"
                data-bs-target="#accountModal">
                <i class="bi bi-person-badge-fill fs-6 text-info flex-shrink-0"></i>
                <span class="text-truncate">Licence :
                    <strong><?= htmlspecialchars($user->getNumeroLicence() ?? 'N/A') ?></strong></span>
            </button>

            <div class="d-flex gap-2">
                <button class="avva-badge-btn avva-badge-news" type="button" data-bs-toggle="modal"
                    data-bs-target="#nouveauteModal">
                    <i class="bi bi-stars"></i>
                    <span>Nouveautés</span>
                </button>
                <button class="avva-badge-btn" type="button" data-bs-toggle="modal" data-bs-target="#settingsModal"
                    title="Paramètres">
                    <i class="bi bi-gear-fill"></i>
                    <span>Options</span>
                </button>
            </div>
        </div>

        <div class="text-center">
            <h1 class="avva-title text-uppercase">CARTOGUIDE</h1>
            <div class="avva-slogan mt-1">
                <span>ÇA</span>
                <a href="https://avva39.fr" target="_blank" rel="noopener">
                    <img src="/assets/images/logo-avva39.png" alt="Logo AVVA39" class="avva-logo">
                </a>
                <span>VA ALLER</span>
            </div>
        </div>
    </header>

    <div class="avva-pro-toolbar">
        <div class="avva-toolbar-group">
            <button class="avva-tool-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#gpxListOffcanvas"
                data-label-mobile="GPX">
                <i class="bi bi-folder-symlink-fill text-info"></i> <span>Mes Parcours GPX</span>
                <span class="badge bg-primary rounded-pill ms-1" id="gpxCountBadge"><?= count($gpxList ?? []) ?></span>
            </button>

            <button class="avva-tool-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#gpxPublicOffcanvas"
                data-label-mobile="Public">
                <i class="bi bi-globe-americas text-warning"></i> <span>Parcours Publics</span>
            </button>

            <button class="avva-tool-btn" type="button" onclick="ouvrirModalSauvegardeGPX()" data-label-mobile="Save">
                <i class="bi bi-cloud-upload-fill text-success"></i> <span>Sauvegarder GPX</span>
            </button>

            <button id="btnOptionsParcours" class="avva-tool-btn" type="button" onclick="ouvrirOptionsParcours()"
                style="display: none;" data-label-mobile="Options">
                <i class="bi bi-sliders text-secondary"></i> <span>Options du parcours</span>
            </button>

            <button class="avva-tool-btn" type="button" onclick="partagerLienParcours()"
                title="Copier le lien direct du parcours" data-label-mobile="Partager">
                <i class="bi bi-share-fill text-warning"></i> <span>Partager</span>
            </button>

            <button class="avva-tool-btn" type="button" onclick="effacerEtNettoyerUrl()" title="Effacer la carte"
                data-label-mobile="Effacer">
                <i class="bi bi-trash-fill text-danger"></i> <span>Effacer</span>
            </button>
        </div>

        <div class="text-white small fw-bold px-2" id="gpxAutoSaveState">
            <i class="bi bi-cloud-check me-1"></i> Sauvegarde auto active
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="app-iframe-area">
            <div id="root-cartoguide" class="w-100 h-100"></div>
        </div>
    </div>
</section>

<div class="modal fade avva-modal-custom modal-fullscreen-sm-down" id="saveGpxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-cloud-upload-fill fs-4"></i> Enregistrer le GPX en ligne
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <form id="formSaveGpx" onsubmit="soumettreFormulaireGPX(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomParcours" class="form-label fw-bold text-white">Nom du parcours *</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="nomParcours"
                            placeholder="Ex: Tour du Lac d'Anterne" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionParcours" class="form-label fw-bold text-white">Description
                            (optionnelle)</label>
                        <textarea class="form-control bg-dark text-white border-secondary" id="descriptionParcours"
                            rows="3" placeholder="Dénivelé, type de sol, recommandations..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill fw-bold"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success rounded-pill fw-bold px-4" id="btnSubmitGpx">
                        <i class="bi bi-check-circle-fill me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end avva-offcanvas" tabindex="-1" id="gpxListOffcanvas" aria-labelledby="gpxListLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-info" id="gpxListLabel">
            <i class="bi bi-collection-fill me-2"></i>Mes Parcours GPX
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            aria-label="Fermer"></button>
    </div>
    <div class="offcanvas-body">
        <div id="listeGpxContainer">
            <?php if (!empty($gpxList)): ?>
                <?php foreach ($gpxList as $gpx): ?>
                    <div class="gpx-card-item id-gpx-<?= $gpx->getId() ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-white mb-0"><?= htmlspecialchars($gpx->getNom()) ?></h6>
                            <small
                                class="text-white fs-7"><?= $gpx->getDateCreation() ? $gpx->getDateCreation()->format('d/m/Y') : '' ?></small>
                        </div>
                        <?php if ($gpx->getDescription()): ?>
                            <p class="small text-white mb-2"><?= htmlspecialchars($gpx->getDescription()) ?></p>
                        <?php endif; ?>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary rounded-pill w-100 fw-bold"
                                onclick="chargerGPXParUrl(<?= $gpx->getId() ?>, '<?= htmlspecialchars($gpx->getFichierUrl(), ENT_QUOTES) ?>')">
                                <i class="bi bi-map-fill me-1"></i> Afficher sur la carte
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 text-white">
                    <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                    Aucun parcours GPX enregistré pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade avva-modal-custom modal-fullscreen-sm-down" id="accountModal" tabindex="-1"
    aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-info d-flex align-items-center gap-2" id="accountModalLabel">
                    <i class="bi bi-person-vcard fs-4"></i> Informations Adhérent & Contacts
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>

            <div class="modal-body pt-3">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-info fw-bold mb-3"><i class="bi bi-person-circle me-1"></i> Identité</h6>

                        <div class="avva-info-item">
                            <div class="avva-info-label">Numéro de Licence</div>
                            <div class="avva-info-val text-info fs-5">
                                <?= htmlspecialchars($user->getNumeroLicence() ?? 'N/A') ?>
                            </div>
                        </div>

                        <div class="avva-info-item">
                            <div class="avva-info-label">Nom & Prénom</div>
                            <div class="avva-info-val">
                                <?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?>
                            </div>
                        </div>

                        <div class="avva-info-item">
                            <div class="avva-info-label">Adresse Courriel</div>
                            <div class="avva-info-val"><?= htmlspecialchars($user->getEmail()) ?></div>
                        </div>

                        <div class="avva-info-item">
                            <div class="avva-info-label">Téléphone Principal</div>
                            <div class="avva-info-val"><?= htmlspecialchars($user->getNumeroTelephone() ?? 'N/A') ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-warning fw-bold mb-3"><i class="bi bi-telephone-plus-fill me-1"></i> Contacts
                        </h6>

                        <div class="avva-info-item border-warning">
                            <div class="avva-info-label text-warning"><i class="bi bi-shield-fill-exclamation me-1"></i>
                                Contact 1</div>
                            <div class="avva-info-val">
                                <a class="text-decoration-none text-white"
                                    href="mailto:webmaster-avva39@outlook.fr">webmaster-avva39@outlook.fr</a>
                            </div>
                        </div>

                        <div class="avva-info-item">
                            <div class="avva-info-label"><i class="bi bi-shield-fill-exclamation me-1"></i> Contact 2
                            </div>
                            <div class="avva-info-val">
                                <a class="text-decoration-none text-white"
                                    href="mailto:presidence-avva39@outlook.fr">presidence-avva39@outlook.fr</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer d-flex flex-column gap-2">
                <a class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold" href="/cartoguide/deconnexion">
                    <i class="bi bi-box-arrow-right me-2"></i>Se Déconnecter
                </a>
                <button type="button" class="btn btn-sm btn-link text-white-50 text-decoration-none"
                    data-bs-dismiss="modal">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade avva-modal-custom modal-fullscreen-sm-down" id="nouveauteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-info"><i class="bi bi-stars me-2"></i>Fonctionnalités Cartoguide
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <strong class="text-info"><i class="bi bi-check2-circle me-1"></i> Sauvegarde en Ligne & Auto
                            :</strong> Vos tracés sont automatiquement sauvegardés en arrière-plan.
                    </li>
                    <li class="mb-3">
                        <strong class="text-info"><i class="bi bi-check2-circle me-1"></i> Partage via URL :</strong>
                        Partagez facilement le lien direct de votre parcours GPX avec les points de passage.
                    </li>
                    <li class="mb-3">
                        <strong class="text-info"><i class="bi bi-check2-circle me-1"></i> Profils de Traçage :</strong>
                        Adaptez automatiquement les parcours selon le type d'activité.
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">J'ai
                    Compris</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade avva-modal-custom modal-fullscreen-sm-down" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-gear-fill me-2 text-info"></i>Gestion du Compte</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="p-2 mb-3 bg-dark rounded border border-secondary">
                    <span class="text-success fw-bold"><i class="bi bi-shield-check me-2"></i>Sécurité :</span>
                    <span class="text-white small">Authentification forte TOTP active sur ce compte.</span>
                </div>

                <div class="avva-danger-box">
                    <h6 class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Abonnement &
                        Résiliation</h6>
                    <p class="text-white-50 small mb-3">
                        En résiliant votre abonnement, votre accès à Cartoguide sera immédiatement interrompu et votre
                        prélèvement annulé.
                    </p>

                    <form action="/cartoguide/resilier-abonnement" method="POST"
                        onsubmit="return confirm('Confirmez-vous l\'annulation automatique de votre abonnement Stripe ?');">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2">
                            <i class="bi bi-x-circle-fill me-2"></i>Résilier mon abonnement
                        </button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill fw-bold"
                    data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade avva-modal-custom-pro modal-fullscreen-sm-down" id="parcoursOptionsModal" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card border-0 rounded-5 overflow-hidden text-white shadow-2xl">

            <div class="modal-header border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-badge">
                        <i class="bi bi-sliders2-vertical"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0 fs-5 text-gradient">Options du parcours</h5>
                        <p class="text-white small m-0">Gérez les paramètres et les accès de votre tracé</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75 custom-close-btn"
                    data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">

                <form id="formUpdateParcours" onsubmit="modifierInfosParcours(event)" class="mb-5">
                    <div class="section-label mb-3">
                        <i class="bi bi-pencil-square text-cyan"></i>
                        <span>Informations principales</span>
                    </div>

                    <input type="hidden" id="editParcoursId" name="parcours_id" value="">

                    <div class="modern-input-group mb-4">
                        <label for="editNomParcours" class="modern-label">Nom du parcours *</label>
                        <div class="input-wrapper">
                            <i class="bi bi-geo-alt-fill input-icon"></i>
                            <input type="text" class="modern-input" id="editNomParcours" name="nom" required
                                placeholder="Ex: Tour du Lac d'Anterne">
                        </div>
                    </div>

                    <div class="modern-input-group mb-4">
                        <label for="editDescriptionParcours" class="modern-label">Description</label>
                        <div class="input-wrapper">
                            <i class="bi bi-text-paragraph input-icon textarea-icon"></i>
                            <textarea class="modern-input modern-textarea" id="editDescriptionParcours"
                                name="description" rows="3"
                                placeholder="Dénivelé, détails, recommandations..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-ultra btn-primary-glow" id="btnSubmitEditParcours">
                            <i class="bi bi-check2-circle"></i>
                            <span>Enregistrer les modifications</span>
                        </button>
                    </div>
                </form>

                <div class="glow-separator my-4"></div>

                <form id="formVisibilityParcours" onsubmit="changerVisibiliteParcours(event)">
                    <div class="section-label mb-3">
                        <i class="bi bi-shield-lock-fill text-cyan"></i>
                        <span>Confidentialité & Accès</span>
                    </div>

                    <input type="hidden" id="visibilityParcoursId" name="parcours_id" value="">

                    <div class="segmented-control-container mb-4">
                        <label class="segmented-option">
                            <input type="radio" name="is_public" value="0" id="visibilitePrive" checked>
                            <div class="option-content option-private">
                                <div class="option-icon">
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                                <div class="option-text">
                                    <span class="option-title">Privé</span>
                                    <span class="option-sub">Accessible uniquement par vous</span>
                                </div>
                                <div class="glow-indicator"></div>
                            </div>
                        </label>

                        <label class="segmented-option">
                            <input type="radio" name="is_public" value="1" id="visibilitePublic">
                            <div class="option-content option-public">
                                <div class="option-icon">
                                    <i class="bi bi-globe2"></i>
                                </div>
                                <div class="option-text">
                                    <span class="option-title">Public</span>
                                    <span class="option-sub">Partageable via lien direct</span>
                                </div>
                                <div class="glow-indicator"></div>
                            </div>
                        </label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-ultra btn-amber-glow" id="btnSubmitVisibility">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Mettre à jour la visibilité</span>
                        </button>
                    </div>
                </form>

            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn-ultra btn-ghost w-100" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end avva-offcanvas" tabindex="-1" id="gpxPublicOffcanvas"
    aria-labelledby="gpxPublicLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-warning" id="gpxPublicLabel">
            <i class="bi bi-globe-americas me-2"></i>Parcours Publics
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            aria-label="Fermer"></button>
    </div>
    <div class="offcanvas-body">
        <div class="mb-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-dark border-secondary text-white"><i class="bi bi-search"></i></span>
                <input type="text" id="gpxSearchInput" class="form-control bg-dark text-white border-secondary"
                    placeholder="Rechercher un parcours...">
            </div>
        </div>

        <div id="listeGpxPublicContainer">
            <div class="text-center py-5 text-white">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Chargement des parcours...
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Copie le lien actuel avec tous ses paramètres dans le presse-papier
     */
    function partagerLienParcours() {
        const urlPartage = window.location.href;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(urlPartage).then(() => {
                afficherToastPartage();
            }).catch(err => {
                console.error("Erreur de copie :", err);
                copierInsecurise(urlPartage);
            });
        } else {
            copierInsecurise(urlPartage);
        }
    }

    function copierInsecurise(texte) {
        const inputTemp = document.createElement("input");
        inputTemp.value = texte;
        document.body.appendChild(inputTemp);
        inputTemp.select();
        try {
            document.execCommand("copy");
            afficherToastPartage();
        } catch (e) {
            alert("Lien du parcours : " + texte);
        }
        document.body.removeChild(inputTemp);
    }

    function afficherToastPartage() {
        const toast = document.getElementById('toastShare');
        if (toast) {
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }
    }
</script>

<script src="/fichier-cartoguide/config.js"></script>
<script src="/fichier-cartoguide/bundle.js"></script>

<script>
    /**
 * Cartoguide Pro - Enregistrement, Modification, Auto-sauvegarde & Parcours Publics
 * (Version Globale Intégrée)
 */
    (function () {
        'use strict';

        // ==========================================
        // 0. INTERCEPTION GLOBALE DE L'HISTORIQUE (pushState / replaceState)
        // ==========================================
        (function (history) {
            const originalPushState = history.pushState;
            const originalReplaceState = history.replaceState;

            history.pushState = function (state) {
                const result = originalPushState.apply(history, arguments);
                window.dispatchEvent(new Event('urlchange'));
                return result;
            };

            history.replaceState = function (state) {
                const result = originalReplaceState.apply(history, arguments);
                window.dispatchEvent(new Event('urlchange'));
                return result;
            };
        })(window.history);

        // ==========================================
        // ÉTAT GLOBAL DE L'APPLICATION & CACHE
        // ==========================================
        const State = {
            currentGpxId: null,
            lastSavedParams: "",
            currentRouteParams: "",
            autoSaveTimeout: null,
            publicSearchTimeout: null,
            isSaving: false,
            gpxListCache: null // Cache local des parcours utilisateur
        };

        // ==========================================
        // 1. INITIALISATION
        // ==========================================
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);

            // A. Restauration de la couche de carte
            const layer = urlParams.get('layer');
            if (layer && typeof window.changeMapLayer === "function") {
                window.changeMapLayer(layer);
                const selectMap = document.getElementById('mapLayerSelect');
                if (selectMap) selectMap.value = layer;
            }

            // B. Restauration du profil de routage
            const profile = urlParams.get('profile');
            if (profile && typeof window.setRoutingProfile === "function") {
                window.setRoutingProfile(profile);
            }

            // C. Récupération de l'ID du parcours actif
            const gpxId = urlParams.get('gpx');
            if (gpxId) {
                State.currentGpxId = parseInt(gpxId, 10);
            }

            State.currentRouteParams = window.location.search;
            State.lastSavedParams = State.currentRouteParams;

            // D. Chargement initial & Écouteurs
            gererAffichageBoutons();
            rafraichirListeGPX();
            attacherEcouteursCarte();
            attacherEcouteursParcoursPublics();
        });

        // ==========================================
        // 2. EXTRACTION DES POINTS & SYNCHRONISATION
        // ==========================================

        function obtenirPointsActuels() {
            let points = [];

            if (window.routingControl && typeof window.routingControl.getWaypoints === "function") {
                const wps = window.routingControl.getWaypoints();
                points = wps.filter(wp => wp && wp.latLng).map(wp => wp.latLng);
            } else if (typeof window.getRoutePoints === "function") {
                points = window.getRoutePoints();
            } else if (window.drawnItems) {
                window.drawnItems.eachLayer(layer => {
                    if (layer.getLatLng) {
                        points.push(layer.getLatLng());
                    } else if (layer.getLatLngs) {
                        const latlngs = layer.getLatLngs();
                        points = points.concat(Array.isArray(latlngs[0]) ? latlngs[0] : latlngs);
                    }
                });
            }

            if (points.length === 0) {
                const urlParams = new URLSearchParams(window.location.search);
                const pointsParam = urlParams.getAll('point');

                if (pointsParam && pointsParam.length > 0) {
                    pointsParam.forEach(ptStr => {
                        const coords = ptStr.split(',');
                        if (coords.length === 2) {
                            const lat = parseFloat(coords[0]);
                            const lng = parseFloat(coords[1]);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                points.push({ lat: lat, lng: lng });
                            }
                        }
                    });
                }
            }

            return points;
        }

        function synchroniserMemoireEtUrl() {
            const points = obtenirPointsActuels();

            if (!points || points.length === 0) {
                State.currentRouteParams = "";
                return false;
            }

            const searchParams = new URLSearchParams(window.location.search);
            searchParams.delete('point');

            points.forEach(pt => {
                const lat = pt.lat !== undefined ? pt.lat : pt[0];
                const lng = pt.lng !== undefined ? pt.lng : pt[1];
                if (lat !== undefined && lng !== undefined) {
                    searchParams.append('point', `${Number(lat).toFixed(6)},${Number(lng).toFixed(6)}`);
                }
            });

            if (State.currentGpxId) {
                searchParams.set('gpx', State.currentGpxId);
            }

            State.currentRouteParams = '?' + searchParams.toString();

            if (State.currentRouteParams.length < 2000 && window.location.search !== State.currentRouteParams) {
                window.history.replaceState({ path: State.currentRouteParams }, '', State.currentRouteParams);
            }

            return true;
        }

        window.onMapStateChanged = function () {
            clearTimeout(State.autoSaveTimeout);

            const badgeState = document.getElementById('gpxAutoSaveState');
            if (badgeState) {
                badgeState.innerHTML = '<i class="bi bi-pencil-fill text-warning me-1"></i> Modification en cours...';
            }

            setTimeout(() => {
                synchroniserMemoireEtUrl();

                State.autoSaveTimeout = setTimeout(() => {
                    autoSauvegarderGPX();
                }, 1000);
            }, 300);
        };

        function attacherEcouteursCarte() {
            window.addEventListener('urlchange', () => {
                if (window.location.search !== State.lastSavedParams && window.location.search !== State.currentRouteParams) {
                    window.onMapStateChanged();
                }
            });
            window.addEventListener('popstate', window.onMapStateChanged);

            const mapDomContainer = document.getElementById('map') || document.querySelector('.leaflet-container');
            if (mapDomContainer && !mapDomContainer._hasAutoSaveListener) {
                const triggerDomChange = () => setTimeout(window.onMapStateChanged, 350);
                mapDomContainer.addEventListener('mouseup', triggerDomChange);
                mapDomContainer.addEventListener('touchend', triggerDomChange);
                mapDomContainer._hasAutoSaveListener = true;
            }

            const initEvents = () => {
                if (window.map) {
                    const mapEvents = [
                        'click', 'dblclick', 'dragend', 'zoomend',
                        'draw:created', 'draw:edited', 'draw:deleted',
                        'pm:create', 'pm:edit', 'pm:remove',
                        'locationfound'
                    ];

                    mapEvents.forEach(evt => {
                        window.map.off(evt, window.onMapStateChanged);
                        window.map.on(evt, window.onMapStateChanged);
                    });
                }

                if (window.routingControl) {
                    const routingEvents = [
                        'routesfound', 'routeselected', 'waypointschanged',
                        'waypointsadded', 'waypointsremoved', 'linechanged', 'resetroutes'
                    ];

                    routingEvents.forEach(evt => {
                        window.routingControl.off(evt, window.onMapStateChanged);
                        window.routingControl.on(evt, window.onMapStateChanged);
                    });

                    if (typeof window.routingControl.setWaypoints === 'function' && !window.routingControl._patchedForAutoSave) {
                        const originalSetWaypoints = window.routingControl.setWaypoints;
                        window.routingControl.setWaypoints = function (...args) {
                            const result = originalSetWaypoints.apply(this, args);
                            window.onMapStateChanged();
                            return result;
                        };
                        window.routingControl._patchedForAutoSave = true;
                    }
                }
            };

            initEvents();

            if (!window.map || !window.routingControl) {
                document.addEventListener('mapInitialized', initEvents);

                let attempts = 0;
                const checkInterval = setInterval(() => {
                    attempts++;
                    initEvents();
                    if ((window.map && window.routingControl) || attempts > 15) {
                        clearInterval(checkInterval);
                    }
                }, 400);
            }
        }

        // ==========================================
        // 3. AUTO-SAUVEGARDE & POPUP CHOIX DE PARCOURS
        // ==========================================

        async function autoSauvegarderGPX() {
            if (State.isSaving) return;

            const badgeState = document.getElementById('gpxAutoSaveState');

            if (!State.currentRouteParams || !State.currentRouteParams.includes('point=')) {
                if (badgeState) badgeState.innerHTML = '';
                return;
            }

            if (State.currentRouteParams === State.lastSavedParams) {
                if (badgeState) {
                    badgeState.innerHTML = '<i class="bi bi-cloud-check-fill text-success me-1"></i> À jour';
                }
                return;
            }

            if (!State.currentGpxId) {
                if (badgeState) {
                    badgeState.innerHTML = '<i class="bi bi-exclamation-circle-fill text-info me-1"></i> Choix du parcours requis';
                }
                window.ouvrirPopupChoixParcours();
                return;
            }

            State.isSaving = true;

            if (badgeState) {
                badgeState.innerHTML = '<i class="bi bi-cloud-sync spinner-border spinner-border-sm text-warning me-1"></i> Sauvegarde...';
            }

            try {
                const response = await fetch('/cartoguide/auto-save-gpx', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        gpx_id: parseInt(State.currentGpxId, 10),
                        route_params: State.currentRouteParams
                    })
                });

                const data = await response.json();

                if (data.success) {
                    State.lastSavedParams = State.currentRouteParams;
                    State.currentGpxId = data.id;

                    mettreAJourParametreURL('gpx', data.id);
                    gererAffichageBoutons();

                    if (badgeState) {
                        badgeState.innerHTML = '<i class="bi bi-cloud-check-fill text-success me-1"></i> Modifié';
                    }

                    rafraichirListeGPX();
                } else if (data.action_required === 'select_target') {
                    window.ouvrirPopupChoixParcours();
                } else {
                    console.error("Erreur d'auto-sauvegarde :", data.message);
                    if (badgeState) {
                        badgeState.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Erreur';
                    }
                }
            } catch (err) {
                console.error("Erreur réseau / synchro GPX :", err);
                if (badgeState) {
                    badgeState.innerHTML = '<i class="bi bi-cloud-slash-fill text-danger me-1"></i> Erreur Sync';
                }
            } finally {
                State.isSaving = false;
            }
        }

        window.ouvrirPopupChoixParcours = async function () {
            let modalEl = document.getElementById('selectGpxModal');

            if (!modalEl) {
                modalEl = document.createElement('div');
                modalEl.id = 'selectGpxModal';
                modalEl.className = 'modal';
                modalEl.setAttribute('tabindex', '-1');
                modalEl.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-white bg-dark border-secondary">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2 text-warning"></i>Quel parcours modifier ?</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-white mb-3">Sélectionnez le parcours auquel appliquer les modifications :</p>
                        <div id="modalGpxListContainer" class="list-group mb-3">
                            <div class="text-center py-3"><span class="spinner-border spinner-border-sm me-2"></span>Chargement de vos parcours...</div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary justify-content-between">
                        <button type="button" class="btn btn-outline-success" onclick="window.fermerModalEtOuvrirCreation()"><i class="bi bi-plus-lg me-1"></i> Nouveau parcours</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </div>
            </div>`;
                document.body.appendChild(modalEl);
            }

            afficherModal(modalEl);
            const listContainer = document.getElementById('modalGpxListContainer');

            if (State.gpxListCache) {
                remplirConteneurModalGpx(listContainer, State.gpxListCache);
                return;
            }

            try {
                const response = await fetch('/cartoguide/liste-gpx-json');
                const data = await response.json();

                if (data.success && data.list) {
                    State.gpxListCache = data.list;
                    remplirConteneurModalGpx(listContainer, State.gpxListCache);
                }
            } catch (e) {
                console.error("Erreur lors de la récupération des parcours :", e);
            }
        };

        function remplirConteneurModalGpx(container, list) {
            if (!container) return;
            if (list && list.length > 0) {
                container.innerHTML = list.map(gpx => `
            <button type="button" class="list-group-item list-group-item-action bg-dark text-white border-secondary d-flex justify-content-between align-items-center mb-1 rounded" onclick="window.assignerEtModifierParcours(${gpx.id})">
                <div>
                    <strong class="d-block">${escapeHtml(gpx.nom)}</strong>
                    <small class="text-white">${gpx.date_modification ? 'Modifié : ' + gpx.date_modification : (gpx.date || '')}</small>
                </div>
                <span class="btn btn-sm btn-warning fw-bold"><i class="bi bi-save me-1"></i> Sélectionner</span>
            </button>
        `).join('');
            } else {
                container.innerHTML = `<div class="alert alert-info small mb-0">Aucun parcours trouvé. Veuillez en créer un nouveau.</div>`;
            }
        }

        window.assignerEtModifierParcours = function (id) {
            State.currentGpxId = parseInt(id, 10);
            mettreAJourParametreURL('gpx', State.currentGpxId);
            gererAffichageBoutons();

            masquerModal('selectGpxModal');

            State.lastSavedParams = "";
            autoSauvegarderGPX();
        };

        window.fermerModalEtOuvrirCreation = function () {
            masquerModal('selectGpxModal');
            window.ouvrirModalSauvegardeGPX();
        };

        // ==========================================
        // 4. CRÉATION & ÉDITION DE PARCOURS
        // ==========================================

        window.ouvrirModalSauvegardeGPX = function () {
            synchroniserMemoireEtUrl();

            if (!State.currentRouteParams || !State.currentRouteParams.includes('point=')) {
                alert("Aucun point d'itinéraire n'a été détecté sur la carte.");
                return;
            }

            const modalEl = document.getElementById('saveGpxModal');
            if (modalEl) {
                afficherModal(modalEl);
            }
        };

        window.soumettreFormulaireGPX = async function (event) {
            event.preventDefault();

            const btnSubmit = document.getElementById('btnSubmitGpx');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enregistrement...';
            }

            synchroniserMemoireEtUrl();

            const nomEl = document.getElementById('nomParcours');
            const descEl = document.getElementById('descriptionParcours');

            const nom = nomEl ? nomEl.value : "";
            const description = descEl ? descEl.value : "";

            try {
                const response = await fetch('/cartoguide/enregistrer-gpx', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        gpx_id: State.currentGpxId,
                        nom: nom,
                        description: description,
                        route_params: State.currentRouteParams
                    })
                });

                const data = await response.json();

                if (data.success) {
                    masquerModal('saveGpxModal');

                    const form = document.getElementById('formSaveGpx');
                    if (form) form.reset();

                    State.currentGpxId = data.gpx ? data.gpx.id : data.id;
                    State.lastSavedParams = State.currentRouteParams;

                    mettreAJourParametreURL('gpx', State.currentGpxId);
                    gererAffichageBoutons();
                    rafraichirListeGPX();
                } else {
                    alert("Erreur : " + (data.message || "Impossible de sauvegarder"));
                }
            } catch (error) {
                alert("Erreur de connexion avec le serveur.");
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Enregistrer';
                }
            }
        };

        window.ouvrirOptionsParcours = function (id = null, nom = '', description = '', isPublic = 0) {
            const targetId = id || State.currentGpxId;

            if (!targetId) {
                alert("Veuillez d'abord choisir ou enregistrer un parcours.");
                return;
            }

            let targetGpx = null;
            if (State.gpxListCache) {
                targetGpx = State.gpxListCache.find(item => item.id == targetId);
            }

            const finalNom = nom || (targetGpx ? targetGpx.nom : '');
            const finalDesc = description || (targetGpx ? (targetGpx.description || '') : '');
            const finalPublic = isPublic !== null && isPublic !== undefined ? isPublic : (targetGpx ? targetGpx.is_public : 0);

            const editIdEl = document.getElementById('editParcoursId');
            const visIdEl = document.getElementById('visibilityParcoursId');
            if (editIdEl) editIdEl.value = targetId;
            if (visIdEl) visIdEl.value = targetId;

            const editNomEl = document.getElementById('editNomParcours');
            const editDescEl = document.getElementById('editDescriptionParcours');
            if (editNomEl) editNomEl.value = finalNom;
            if (editDescEl) editDescEl.value = finalDesc;

            const radioPublic = document.getElementById('visibilitePublic');
            const radioPrive = document.getElementById('visibilitePrive');
            if (parseInt(finalPublic, 10) === 1) {
                if (radioPublic) radioPublic.checked = true;
            } else {
                if (radioPrive) radioPrive.checked = true;
            }

            const modalEl = document.getElementById('parcoursOptionsModal');
            if (modalEl) {
                afficherModal(modalEl);
            }
        };

        window.modifierInfosParcours = async function (event) {
            event.preventDefault();

            const btnSubmit = document.getElementById('btnSubmitEditParcours');
            const originalText = btnSubmit ? btnSubmit.innerHTML : '';

            const payload = {
                parcours_id: document.getElementById('editParcoursId').value,
                nom: document.getElementById('editNomParcours').value,
                description: document.getElementById('editDescriptionParcours').value
            };

            try {
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Enregistrement...`;
                }

                const response = await fetch('/cartoguide/modifier-parcours', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    rafraichirListeGPX();
                    masquerModal('parcoursOptionsModal');
                } else {
                    alert("Erreur : " + (data.message || "Impossible de sauvegarder les modifications."));
                }
            } catch (error) {
                console.error("Erreur lors de la modification des données :", error);
                alert("Erreur de communication avec le serveur.");
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                }
            }
        };

        window.changerVisibiliteParcours = async function (event) {
            event.preventDefault();

            const btnSubmit = document.getElementById('btnSubmitVisibility');
            const originalText = btnSubmit ? btnSubmit.innerHTML : '';

            const isPublicRadio = document.querySelector('input[name="is_public"]:checked');
            const isPublicValue = isPublicRadio ? isPublicRadio.value : 0;

            const payload = {
                parcours_id: document.getElementById('visibilityParcoursId').value,
                is_public: parseInt(isPublicValue, 10)
            };

            try {
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Application...`;
                }

                const response = await fetch('/cartoguide/changer-visibilite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    rafraichirListeGPX();
                    masquerModal('parcoursOptionsModal');
                } else {
                    alert("Erreur : " + (data.message || "Impossible de modifier la visibilité."));
                }
            } catch (error) {
                console.error("Erreur visibilité :", error);
                alert("Erreur de connexion avec le serveur.");
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                }
            }
        };

        // ==========================================
        // 5. GESTION DES PARCOURS PUBLICS (AJOUT)
        // ==========================================

        function attacherEcouteursParcoursPublics() {
            const offcanvasEl = document.getElementById('gpxPublicOffcanvas');
            const searchInput = document.getElementById('gpxSearchInput');

            if (offcanvasEl) {
                offcanvasEl.addEventListener('show.bs.offcanvas', () => {
                    rafraichirListeGPXPublics();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(State.publicSearchTimeout);
                    State.publicSearchTimeout = setTimeout(() => {
                        rafraichirListeGPXPublics(e.target.value.trim());
                    }, 300);
                });
            }
        }

        async function rafraichirListeGPXPublics(query = '') {
            const container = document.getElementById('listeGpxPublicContainer');
            if (!container) return;

            try {
                const response = await fetch(`/cartoguide/liste-parcours-publics?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || "Impossible de charger les parcours publics");
                }

                if (!data.list || data.list.length === 0) {
                    container.innerHTML = `
                <div class="text-center py-5 text-white">
                    <i class="bi bi-compass fs-1 d-block mb-2"></i>
                    Aucun parcours public trouvé.
                </div>`;
                    return;
                }

                container.innerHTML = data.list.map(gpx => `
                <div class="gpx-card-item id-gpx-${gpx.id} mb-2 p-2 border border-secondary bg-dark rounded">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-bold text-white mb-0 text-truncate">${escapeHtml(gpx.nom)}</h6>
                        <small class="text-white fs-7 ms-2">${escapeHtml(gpx.date)}</small>
                    </div>
                    
                    <div class="small text-info mb-2">
                        <i class="bi bi-person-fill me-1"></i>
                        ${escapeHtml(gpx.auteur)}
                    </div>

                    ${gpx.description ? `<p class="small text-white mb-2 text-truncate">${escapeHtml(gpx.description)}</p>` : ''}

                    <div class="d-flex flex-column gap-2">
                        <button class="btn btn-sm btn-primary rounded-pill w-100 fw-bold"
                                onclick="window.chargerGPXParUrl(${gpx.id}, '${escapeJsString(gpx.url)}')">
                            <i class="bi bi-map-fill me-1"></i> Afficher sur la carte
                        </button>
                        <button type="button" 
                                class="btn btn-sm btn-outline-success flex-fill fw-bold rounded-2"
                                onclick="window.reprendreParcours(${gpx.id}, this)">
                            <i class="bi bi-download me-1"></i> Reprendre
                        </button>
                    </div>
                </div>
            `).join('');

            } catch (error) {
                console.error("Erreur parcours publics :", error);
                container.innerHTML = `
                <div class="alert alert-danger small py-2">
                    <i class="bi bi-exclamation-triangle me-1"></i> Erreur lors du chargement des parcours.
                </div>`;
            }
        }

        // Exposer explicitement la fonction globale pour la rendre disponible
        window.rafraichirListeGPXPublics = rafraichirListeGPXPublics;

        // ==========================================
        // 6. FONCTIONS UTILITAIRES & MODALES
        // ==========================================

        function gererAffichageBoutons() {
            const btnOptions = document.getElementById('btnOptionsParcours');
            if (btnOptions) {
                if (State.currentGpxId) {
                    btnOptions.style.display = 'inline-flex';
                } else {
                    btnOptions.style.display = 'none';
                }
            }
        }

        function afficherModal(modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }

        function masquerModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
            }
        }

        window.chargerGPXParUrl = function (id, routeParams) {
            if (!routeParams) return;

            const targetParams = new URLSearchParams(routeParams);
            if (id) {
                targetParams.set('gpx', id);
            }

            window.location.href = window.location.origin + window.location.pathname + '?' + targetParams.toString();
        };

        window.effacerEtNettoyerUrl = function () {
            if (typeof window.clearTrace === "function") {
                window.clearTrace();
            }

            State.currentGpxId = null;
            State.lastSavedParams = "";
            State.currentRouteParams = "";
            State.gpxListCache = null;

            gererAffichageBoutons();

            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.pushState({ path: cleanUrl }, '', cleanUrl);
            window.location.reload();
        };

        function mettreAJourParametreURL(key, value) {
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set(key, value);
            if (newUrl.href.length < 2000) {
                window.history.pushState({ path: newUrl.href }, '', newUrl.href);
            }
        }

        async function rafraichirListeGPX() {
            try {
                const response = await fetch('/cartoguide/liste-gpx-json');
                const data = await response.json();

                if (data.success) {
                    State.gpxListCache = data.list;

                    const container = document.getElementById('listeGpxContainer');
                    const badge = document.getElementById('gpxCountBadge');

                    if (badge) badge.innerText = data.list.length;
                    if (!container) return;

                    if (data.list.length === 0) {
                        container.innerHTML = `<div class="text-center py-5 text-white"><i class="bi bi-geo-alt fs-1 d-block mb-2"></i>Aucun parcours enregistré.</div>`;
                        return;
                    }

                    container.innerHTML = data.list.map(gpx => {
                        const activeClass = (gpx.id == State.currentGpxId) ? 'active border-primary fw-bold' : '';
                        const dateAffichage = gpx.date_modification
                            ? `<i class="bi bi-clock-history me-1"></i>Modifié : ${escapeHtml(gpx.date_modification)}`
                            : (gpx.date ? `<i class="bi bi-calendar3 me-1"></i>${escapeHtml(gpx.date)}` : '');

                        const isPublic = gpx.is_public ? 1 : 0;
                        const badgeVisibilite = isPublic
                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><i class="bi bi-globe2 me-1"></i>Public</span>'
                            : '<span class="badge bg-secondary-subtle text-muted border border-secondary-subtle rounded-pill"><i class="bi bi-lock-fill me-1"></i>Privé</span>';

                        return `
                    <div class="gpx-card-item id-gpx-${gpx.id} ${activeClass} mb-2 p-2 border border-secondary bg-dark rounded">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="text-white mb-0 text-truncate">${escapeHtml(gpx.nom)}</h6>
                            ${badgeVisibilite}
                        </div>
                        <div class="mb-2">
                            <small class="text-white fs-7 d-block mb-1">${dateAffichage}</small>
                            ${gpx.description ? `<p class="small text-white mb-0 text-truncate">${escapeHtml(gpx.description)}</p>` : ''}
                        </div>
                        <div class="d-flex gap-1 mt-2">
                            <button class="btn btn-sm btn-primary w-100 fw-bold" onclick="window.chargerGPXParUrl(${gpx.id}, '${escapeJsString(gpx.url)}')">
                                <i class="bi bi-map-fill me-1"></i> Afficher
                            </button>
                            <button class="btn btn-sm btn-outline-warning" title="Options / Éditer" onclick="window.ouvrirOptionsParcours(${gpx.id}, '${escapeJsString(gpx.nom)}', '${escapeJsString(gpx.description || '')}', ${isPublic})">
                                <i class="bi bi-gear-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="window.supprimerParcours(${gpx.id})">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </div>`;
                    }).join('');
                }
            } catch (err) {
                console.error("Erreur rafraîchissement liste GPX :", err);
            }
        }

        window.supprimerParcours = async function (id) {
            if (!confirm("Êtes-vous sûr de vouloir supprimer ce parcours ? Cette action est irréversible.")) {
                return;
            }

            try {
                const response = await fetch('/cartoguide/supprimer-gpx', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ gpx_id: parseInt(id, 10) })
                });

                const data = await response.json();

                if (data.success) {
                    if (State.currentGpxId == id) {
                        window.effacerEtNettoyerUrl();
                    } else {
                        rafraichirListeGPX();
                    }
                } else {
                    alert("Erreur : " + (data.message || "Impossible de supprimer le parcours."));
                }
            } catch (error) {
                console.error("Erreur lors de la suppression :", error);
                alert("Erreur de communication avec le serveur.");
            }
        };

        // ==========================================
        // 7. SÉCURITÉ & ÉCHAPPEMENT
        // ==========================================

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m]));
        }

        function escapeJsString(str) {
            if (!str) return '';
            return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

    })();

    /**
     * Helper : Affiche une popup/modal Bootstrap moderne pour les notifications (Succès / Erreur)
     */
    function afficherToastModal(titre, message, type = 'success') {
        const isSuccess = type === 'success';
        const iconClass = isSuccess ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger';
        const btnClass = isSuccess ? 'btn-success' : 'btn-danger';

        const toastHtml = `
        <div class="modal fade" id="modalNotification" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content bg-dark text-white border border-secondary rounded-4 shadow">
                    <div class="modal-body text-center p-4">
                        <i class="bi ${iconClass} display-4 mb-3 d-block"></i>
                        <h5 class="fw-bold mb-2">${titre}</h5>
                        <p class="small text-white mb-4">${message}</p>
                        <button type="button" class="btn btn-sm ${btnClass} w-100 rounded-pill fw-bold" data-bs-dismiss="modal">
                            D'accord
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        // Nettoyage d'une ancienne notification
        const existingNotification = document.getElementById('modalNotification');
        if (existingNotification) existingNotification.remove();

        document.body.insertAdjacentHTML('beforeend', toastHtml);

        const notificationEl = document.getElementById('modalNotification');
        const bsModal = new bootstrap.Modal(notificationEl);
        bsModal.show();

        // Suppression du DOM à la fermeture
        notificationEl.addEventListener('hidden.bs.modal', () => notificationEl.remove());
    }

    /**
     * Action globale avec Modal Bootstrap 5
     */
    window.reprendreParcours = function (idParcours, btnEl = null) {
        if (!idParcours) return;

        // Création de la modal de confirmation dynamique
        const modalHtml = `
        <div class="modal fade" id="modalConfirmReprise" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white border border-secondary rounded-4 shadow">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="bi bi-download me-2"></i>Reprendre le parcours
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Voulez-vous enregistrer une copie de ce parcours dans votre compte personnel ?</p>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-sm btn-success rounded-pill fw-bold" id="btnConfirmRepriseAction">
                            <i class="bi bi-check-lg me-1"></i> Oui, confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        // Supprimer une ancienne modal de confirmation si elle existe
        const existingModal = document.getElementById('modalConfirmReprise');
        if (existingModal) existingModal.remove();

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modalEl = document.getElementById('modalConfirmReprise');
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();

        // Événement au clic sur "Confirmer"
        document.getElementById('btnConfirmRepriseAction').addEventListener('click', async () => {
            bsModal.hide();

            let originalHtml = '';
            if (btnEl) {
                originalHtml = btnEl.innerHTML;
                btnEl.disabled = true;
                btnEl.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Reprise...`;
            }

            try {
                const response = await fetch('/cartoguide/reprendre-parcours-json', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ id_parcours: idParcours })
                });

                const res = await response.json();

                if (response.ok && res.success) {
                    // Remplacement de l'alert de succès
                    afficherToastModal(
                        "Succès !",
                        res.message || "Parcours ajouté avec succès à votre liste !",
                        'success'
                    );

                    if (typeof window.rafraichirMesParcours === 'function') {
                        window.rafraichirMesParcours();
                    }
                } else {
                    // Remplacement de l'alert d'erreur métier
                    afficherToastModal(
                        "Oups...",
                        res.message || "Impossible de reprendre ce parcours.",
                        'error'
                    );
                }
            } catch (err) {
                console.error("Erreur lors de la reprise du parcours :", err);
                // Remplacement de l'alert d'erreur réseau
                afficherToastModal(
                    "Erreur réseau",
                    "Une erreur s'est produite lors de la communication avec le serveur.",
                    'error'
                );
            } finally {
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.innerHTML = originalHtml;
                }
                modalEl.addEventListener('hidden.bs.modal', () => modalEl.remove());
            }
        });
    };
</script>
<script>
    /* ==========================================================================
       GESTIONNAIRE PWA UNIFIÉ (ACCUEIL & CARTOGUIDE) - VERSION ULTRA ROBUSTE
       ========================================================================== */

    // Capture globale de l'événement d'installation Android/Chrome (avant le chargement)
    window.avvaPwaPrompt = window.avvaPwaPrompt || null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        window.avvaPwaPrompt = e;
    });

    (() => {
        // 1. Détection de route
        const currentPath = window.location.pathname.toLowerCase();
        const isCartoguide = currentPath.includes('/cartoguide');

        // Configuration dynamique des sélecteurs
        const prefix = isCartoguide ? 'cartoguide-pwa-' : 'pwa-';
        const openClass = isCartoguide ? 'cartoguide-is-open' : 'is-open';
        const discretClass = isCartoguide ? 'cartoguide-pwa-discret' : 'pwa-discret';

        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        // 2. Sélecteur d'éléments DOM dynamique
        const getDOM = () => ({
            modal: document.getElementById(`${prefix}component-modal`),
            btnOpen: document.getElementById(`${prefix}action-open`),
            btnClose: document.getElementById(`${prefix}action-close`),
            btnInstall: document.getElementById(`${prefix}action-install`),
            txtStatus: document.getElementById(`${prefix}text-status`),
            guideIos: document.getElementById(`${prefix}guide-ios`),
            guideGeneric: document.getElementById(`${prefix}guide-generic`),
            txtInstructions: document.getElementById(`${prefix}text-instructions`)
        });

        // 3. Modale (Ouverture / Fermeture)
        const toggleModal = (isOpen) => {
            const DOM = getDOM();
            if (!DOM.modal) return;

            if (isOpen) {
                DOM.modal.classList.add(openClass);
                DOM.modal.setAttribute('aria-hidden', 'false');
            } else {
                DOM.modal.classList.remove(openClass);
                DOM.modal.setAttribute('aria-hidden', 'true');
            }
        };

        // 4. Adaptation de l'interface (iOS vs Android / Manuel vs Automatique)
        const updatePlatformUX = () => {
            const DOM = getDOM();

            if (isIOS) {
                if (DOM.txtStatus) {
                    DOM.txtStatus.textContent = isCartoguide
                        ? "📱 iOS : Ajouter le Cartoguide à l'écran d'accueil"
                        : "📱 Configuration : Apple iOS";
                }
                if (DOM.guideIos) DOM.guideIos.style.display = "block";
                if (DOM.guideGeneric) DOM.guideGeneric.style.display = "none";
            } else {
                if (DOM.txtStatus) {
                    DOM.txtStatus.textContent = isCartoguide
                        ? "🗺️ Installation rapide du Cartoguide"
                        : "🤖 Configuration : Android / Standard";
                }
                if (DOM.guideIos) DOM.guideIos.style.display = "none";
                if (DOM.guideGeneric) DOM.guideGeneric.style.display = "block";

                // Si le prompt natif est prêt, on met en avant le bouton d'installation directe
                if (window.avvaPwaPrompt && DOM.btnInstall) {
                    DOM.btnInstall.style.display = 'block';
                    if (DOM.txtInstructions) DOM.txtInstructions.style.display = 'none';
                }
            }
        };

        // 5. Action au clic sur "Installer"
        const triggerInstall = async () => {
            if (!window.avvaPwaPrompt) {
                // Si pas de prompt natif (ex: Firefox ou navigateur non compatible), on garde la modale avec les instructions manuelles
                toggleModal(true);
                return;
            }

            try {
                await window.avvaPwaPrompt.prompt();
                const { outcome } = await window.avvaPwaPrompt.userChoice;

                if (outcome === 'accepted') {
                    toggleModal(false);
                    const DOM = getDOM();
                    if (DOM.btnOpen) DOM.btnOpen.style.display = 'none';
                }
            } catch (err) {
                console.error("[PWA] Erreur lors de l'installation :", err);
            } finally {
                window.avvaPwaPrompt = null;
            }
        };

        // 6. Délégation globale d'événements pour les clics
        const initEvents = () => {
            document.addEventListener('click', (e) => {
                // Clic sur le bouton flottant (FAB) pour ouvrir
                const btnOpenTarget = e.target.closest(`#${prefix}action-open`);
                if (btnOpenTarget) {
                    e.preventDefault();
                    e.stopPropagation();
                    updatePlatformUX();
                    toggleModal(true);
                    return;
                }

                // Clic sur le bouton de fermeture ou le fond gris (overlay)
                const btnCloseTarget = e.target.closest(`#${prefix}action-close`);
                if (btnCloseTarget || e.target.id === `${prefix}component-modal`) {
                    e.preventDefault();
                    toggleModal(false);
                    return;
                }

                // Clic sur le bouton d'installation dans la modale
                const btnInstallTarget = e.target.closest(`#${prefix}action-install`);
                if (btnInstallTarget) {
                    e.preventDefault();
                    triggerInstall();
                    return;
                }
            });

            // Fermeture avec la touche Échap
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') toggleModal(false);
            });

            // Masquer si l'application vient d'être installée
            window.addEventListener('appinstalled', () => {
                toggleModal(false);
                const DOM = getDOM();
                if (DOM.btnOpen) DOM.btnOpen.style.display = 'none';
            });

            // Effet discret au scroll
            let lastScrollTop = 0;
            window.addEventListener('scroll', () => {
                const DOM = getDOM();
                if (!DOM.btnOpen) return;

                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    DOM.btnOpen.classList.add(discretClass);
                } else {
                    DOM.btnOpen.classList.remove(discretClass);
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }, { passive: true });
        };

        // 7. Initialisation
        const init = () => {
            initEvents();
            updatePlatformUX();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
<script>
    (function () {
        var fullUrl = window.location.href.toLowerCase();
        var hasCartoguide = fullUrl.indexOf('cartoguide') !== -1;

        // Détermination du chemin du fichier
        var scriptPath = hasCartoguide
            ? '/assets/js/wpa-cartoguide.js'
            : '/assets/js/wpa-avva39.js';

        console.log('[PWA Loader] URL détectée :', fullUrl);
        console.log('[PWA Loader] Chargement du fichier :', scriptPath);

        var v = Date.now();
        var script = document.createElement('script');
        script.src = scriptPath + '?v=' + v;
        script.defer = true;

        script.onload = function () {
            console.log('[PWA Loader] ✅ Fichier chargé avec succès :', scriptPath);
        };

        script.onerror = function () {
            console.error('[PWA Loader] ❌ Échec de chargement (404/Réseau) sur :', scriptPath);
        };

        (document.head || document.documentElement).appendChild(script);
    })();
</script>