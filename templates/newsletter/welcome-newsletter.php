<style>
    /* --- CONTEXTE SPATIAL ET ÉTOILES --- */
    body {
        margin: 0;
        background: #090a0f;
        overflow-x: hidden;
    }

    .stars-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
        background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
    }

    .glow-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.35;
        pointer-events: none;
    }

    .halo-1 {
        width: 350px;
        height: 350px;
        background: #0dcaf0;
        top: 20%;
        left: 15%;
        animation: floatOrb 12s infinite alternate ease-in-out;
    }

    .halo-2 {
        width: 400px;
        height: 400px;
        background: #6610f2;
        bottom: 10%;
        right: 15%;
        animation: floatOrb 16s infinite alternate-reverse ease-in-out;
    }

    @keyframes floatOrb {
        0% {
            transform: translate(0, 0) scale(1);
        }

        100% {
            transform: translate(40px, 50px) scale(1.15);
        }
    }

    /* --- CARTE EN VERRE TREMPÉ (GLASSMORPHISM) --- */
    .glass-card-cosmic {
        max-width: 460px;
        width: 100%;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes cardAppear {
        from {
            opacity: 0;
            transform: translateY(25px) scale(0.96);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* --- TEXTES ET TYPOGRAPHIE --- */
    .text-gradient {
        background: linear-gradient(135deg, #ffffff 30%, #a5c0ee 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .extra-small {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    /* --- ICÔNE ANIMÉE --- */
    .icon-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 85px;
        height: 85px;
        border-radius: 50%;
        background: rgba(13, 202, 240, 0.1);
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .icon-main {
        font-size: 2.2rem;
        transition: transform 0.4s ease;
    }

    .glass-card-cosmic:hover .icon-main {
        transform: scale(1.1) rotate(-10deg);
    }

    .icon-pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 1px solid rgba(13, 202, 240, 0.4);
        animation: pulseRing 2.5s infinite ease-out;
    }

    @keyframes pulseRing {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }

        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    /* --- BOUTONS --- */
    .btn-cosmic-glow {
        background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
        border: none;
        box-shadow: 0 4px 20px rgba(13, 202, 240, 0.35);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-cosmic-glow:hover {
        background: linear-gradient(135deg, #31d2f2 0%, #0b5ed7 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 202, 240, 0.5);
    }

    .btn-cosmic-glow:hover .icon-arrow {
        transform: translateX(4px);
    }

    .icon-arrow {
        transition: transform 0.3s ease;
    }

    .btn-ghost-subtle {
        transition: all 0.2s ease;
        background: transparent;
    }

    .btn-ghost-subtle:hover {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.05);
    }
</style>
<!-- Arrière-plan spatial animé -->
<div class="stars-container">
    <div class="glow-orb halo-1"></div>
    <div class="glow-orb halo-2"></div>
</div>

<div class="container py-5 min-vh-100 d-flex justify-content-center align-items-center position-relative z-1">
    <div class="glass-card-cosmic p-4 p-md-5 text-center shadow-2xl">

        <!-- Icône animée -->
        <div class="icon-wrapper mb-4">
            <div class="icon-pulse-ring"></div>
            <i class="fa-solid fa-paper-plane text-info icon-main"></i>
        </div>

        <!-- Contenu texte -->
        <span
            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 mb-3 extra-small text-uppercase tracking-wider">
            <i class="fa-solid fa-sparkles me-1"></i> Communauté MétéAstro
        </span>

        <h2 class="text-gradient fw-bold mb-3">Restez la tête dans les étoiles !</h2>

        <p class="text-white-50 mb-4 leading-relaxed fs-6">
            Recevez nos alertes météo astronomiques, les prévisions de nuit claire et les actualités des événements
            célestes
            en avant-première.
        </p>

        <!-- Formulaire d'action -->
        <form action="" method="POST" class="d-grid gap-3">
            <button type="submit" name="accept_newsletter" value="1"
                class="btn btn-cosmic-glow py-3 px-4 fw-bold text-white rounded-4 d-flex align-items-center justify-content-center gap-2">
                <span>Oui, je m'abonne !</span>
                <i class="fa-solid fa-arrow-right icon-arrow"></i>
            </button>

            <button type="submit" name="accept_newsletter" value="0"
                class="btn btn-ghost-subtle py-2 text-white-50 text-decoration-none rounded-4">
                Non merci, passer cette étape
            </button>
        </form>

        <!-- Note de confidentialité -->
        <div class="mt-4 pt-3 border-top border-white border-opacity-10">
            <small class="text-white-50 extra-small d-block">
                <i class="fa-solid fa-shield-halved me-1 text-info"></i> Pas de spam. Désabonnement possible en un clic.
            </small>
        </div>

    </div>
</div>