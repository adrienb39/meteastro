<style>
    /* Arrière-plan spatial & dégradé moderne */
    .meteastro-404-container {
        min-height: 80vh;
        background: radial-gradient(circle at center, #1b1e38 0%, #0d0e17 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        border-radius: 24px;
    }

    /* Effet Néon / Glow sur le chiffre 404 */
    .glow-text {
        font-size: clamp(6rem, 15vw, 10rem);
        font-weight: 900;
        line-height: 1;
        background: linear-gradient(135deg, #a855f7, #3b82f6, #06b6d4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 50px rgba(168, 85, 247, 0.3);
    }

    /* Animation de flottaison douce pour l'icône */
    .floating-icon {
        animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(5deg); }
    }

    /* Bouton personnalisé avec effet survol */
    .btn-meteastro {
        background: linear-gradient(135deg, #6366f1, #a855f7);
        color: #fff;
        border: none;
        border-radius: 50rem;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
    }

    .btn-meteastro:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(168, 85, 247, 0.6);
        color: #fff;
    }

    /* Étoiles subtiles en arrière-plan */
    .stars-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-image: 
            radial-gradient(2px 2px at 20px 30px, #ffffff, rgba(0,0,0,0)),
            radial-gradient(2px 2px at 40px 70px, #rgba(255,255,255,0.5), rgba(0,0,0,0)),
            radial-gradient(1px 1px at 90px 40px, #ffffff, rgba(0,0,0,0)),
            radial-gradient(2px 2px at 160px 120px, #rgba(255,255,255,0.8), rgba(0,0,0,0));
        background-size: 200px 200px;
        opacity: 0.3;
    }
</style>

<div class="d-flex justify-content-center align-items-center meteastro-404-container p-4">
    <div class="stars-bg"></div>
    
    <div class="text-center position-relative z-1 max-w-md">
        <!-- Illustration animée Météo / Astro -->
        <div class="floating-icon mb-3">
            <svg width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-info">
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.93 4.93 1.41 1.41"></path>
                <path d="m17.66 17.66 1.41 1.41"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.34 17.66-1.41 1.41"></path>
                <path d="m19.07 4.93-1.41 1.41"></path>
                <circle cx="12" cy="12" r="4" fill="rgba(6, 182, 212, 0.2)"></circle>
            </svg>
        </div>

        <!-- Grand 404 Dégradé -->
        <div class="glow-text my-n2">404</div>

        <!-- Textes -->
        <h1 class="fw-bold fs-2 mb-3 text-white">Perdu dans le cosmos ?</h1>
        <p class="text-white-50 mb-4 fs-6 style-description">
            La page que vous cherchez s'est évaporée dans l'espace ou n'a jamais existé.
        </p>

        <!-- Actions -->
        <div class="d-flex justify-content-center gap-3">
            <a href="/" class="btn btn-meteastro">
                Retourner à l'accueil
            </a>
        </div>
    </div>
</div>