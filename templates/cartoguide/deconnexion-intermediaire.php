<style>
    /* ==========================================================
   CHARTE D'IDENTITÉ & VARIABLES AVVA39
   ========================================================== */
    :root {
        --avva-blue: #0066cc;
        --avva-blue-dark: #004d99;
        --avva-green: #28a745;
        --avva-green-light: #e8f5e9;
        --avva-text-dark: #1e293b;
        --avva-text-muted: #64748b;
        --avva-bg-input: #f8fafc;
        --avva-border-color: #e2e8f0;
        --avva-radius-card: 28px;
        --avva-radius-elem: 14px;
    }

    /* Wrapper Global Centré */
    .avva-login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    /* Carte Principale Glassmorphism & Entrée Animée */
    .avva-card {
        width: 100%;
        max-width: 440px;
        margin: 90px auto;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: var(--avva-radius-card);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.6) inset;
        overflow: hidden;
        position: relative;
        animation: avvaCardEnter 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .avva-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(0, 102, 204, 0.18), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
    }

    /* Barre de dégradé supérieure animée */
    .avva-top-bar {
        height: 6px;
        width: 100%;
        background: linear-gradient(90deg, var(--avva-blue), var(--avva-green), var(--avva-blue));
        background-size: 200% 100%;
        animation: avvaGradientShift 4s ease infinite;
    }

    .avva-card-body {
        padding: 2.25rem 2rem 1.5rem 2rem;
    }

    /* En-tête Carte */
    .avva-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .avva-logo-box {
        margin-bottom: 1rem;
        display: inline-block;
    }

    .avva-logo {
        max-height: 80px;
        width: auto;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.06));
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avva-logo:hover {
        transform: scale(1.08) rotate(-2deg);
    }

    .avva-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--avva-text-dark);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .avva-description {
        font-size: 0.875rem;
        color: var(--avva-text-muted);
        line-height: 1.4;
    }

    /* Conteneur et Animation de l'icône de succès */
    .avva-success-icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 68px;
        height: 68px;
        background-color: var(--avva-green-light);
        color: var(--avva-green);
        border-radius: 50%;
        font-size: 2.4rem;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        animation: avvaPulseSuccess 2.5s infinite ease-in-out;
    }

    /* Zone de chargement et spinner */
    .avva-redirect-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        background-color: #f8fafc;
        border: 1px solid var(--avva-border-color);
        padding: 0.85rem 1rem;
        border-radius: var(--avva-radius-elem);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .avva-redirect-box:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .avva-spinner {
        width: 20px;
        height: 20px;
        border: 2.5px solid rgba(0, 102, 204, 0.15);
        border-top-color: var(--avva-blue);
        border-radius: 50%;
        animation: avva-spin 0.8s linear infinite;
        flex-shrink: 0;
    }

    .avva-redirect-text {
        font-size: 0.825rem;
        color: var(--avva-text-muted);
        font-weight: 500;
    }

    /* Pied de Carte */
    .avva-card-footer {
        padding: 1.1rem 2rem;
        background-color: rgba(248, 250, 252, 0.8);
        border-top: 1px solid var(--avva-border-color);
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .avva-footer-text {
        font-size: 0.75rem;
        color: var(--avva-text-muted);
    }

    .avva-link {
        color: var(--avva-blue);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: color 0.2s ease;
    }

    .avva-link:hover {
        color: var(--avva-blue-dark);
    }

    .avva-home-icon {
        transition: transform 0.3s ease;
    }

    .avva-link:hover .avva-home-icon {
        transform: scale(1.2);
    }

    /* ==========================================================
   ANIMATIONS EN CASCADE (STAGGERED ANIMATIONS)
   ========================================================== */

    .avva-anim-1,
    .avva-anim-2,
    .avva-anim-3,
    .avva-anim-4,
    .avva-anim-5 {
        opacity: 0;
        animation: avvaFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .avva-anim-1 {
        animation-delay: 0.1s;
    }

    .avva-anim-2 {
        animation-delay: 0.2s;
    }

    .avva-anim-3 {
        animation-delay: 0.3s;
    }

    .avva-anim-4 {
        animation-delay: 0.4s;
    }

    .avva-anim-5 {
        animation-delay: 0.5s;
    }

    .avva-anim-pop {
        animation: avvaPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        animation-delay: 0.2s;
    }

    /* Keyframes */
    @keyframes avvaCardEnter {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.96);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes avvaFadeUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes avvaGradientShift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    @keyframes avvaPop {
        from {
            opacity: 0;
            transform: scale(0.6);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes avvaPulseSuccess {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 6px 22px rgba(40, 167, 69, 0.35);
        }
    }

    @keyframes avva-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<meta http-equiv="refresh" content="3;url=/" />

<div class="avva-login-wrapper">
    <div class="avva-card">

        <div class="avva-top-bar"></div>

        <div class="avva-card-body text-center py-4">

            <header class="avva-header mb-3 avva-anim-1">
                <div class="avva-logo-box mb-2">
                    <img src="/assets/images/logo-avva39.png" alt="Logo AVVA39" class="avva-logo"
                        style="max-height: 70px;">
                </div>
                <h1 class="avva-title fs-3">Cartoguide</h1>
            </header>

            <div class="avva-success-icon-wrapper my-3 avva-anim-pop">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <h2 class="fw-bold fs-4 text-dark mb-2 avva-anim-3">Déconnexion Réussie</h2>

            <p class="avva-description mb-4 avva-anim-3">
                <?= htmlspecialchars($successMessage ?? "Vous avez été déconnecté en toute sécurité.") ?>
            </p>

            <div class="avva-redirect-box avva-anim-4">
                <div class="avva-spinner" role="status"></div>
                <span class="avva-redirect-text">Redirection vers l'accueil dans un instant...</span>
            </div>

        </div>

        <footer class="avva-card-footer avva-anim-5">
            <span class="avva-footer-text">Si la redirection ne fonctionne pas :</span>
            <a href="/" class="avva-link">
                <i class="bi bi-house-door-fill me-1 avva-home-icon"></i> Retourner à l'accueil
            </a>
        </footer>

    </div>
</div>
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