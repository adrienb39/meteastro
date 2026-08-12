<style>
    /* ==========================================================
   CHARTE D'IDENTITÉ & VARIABLES AVVA39
   ========================================================== */
    :root {
        --avva-blue: #0066cc;
        --avva-blue-dark: #004d99;
        --avva-green: #28a745;
        --avva-green-dark: #218838;
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

    /* Carte Principale Glassmorphism & Animation de démarrage */
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

    .avva-card-wide {
        max-width: 480px;
    }

    .avva-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(0, 102, 204, 0.18), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
    }

    /* Barre supérieure dégradée animée en continu */
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
        margin-bottom: 1.25rem;
    }

    .avva-logo-box {
        margin-bottom: 0.75rem;
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

    .avva-subtitle {
        font-size: 0.875rem;
        color: var(--avva-text-muted);
        margin-top: 0.25rem;
    }

    /* Badge d'étape */
    .avva-step-badge {
        display: table;
        margin: 0 auto 1rem auto;
        background-color: #f1f5f9;
        border: 1px solid var(--avva-border-color);
        padding: 0.4rem 0.9rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--avva-text-dark);
        transition: transform 0.25s ease;
    }

    .avva-step-badge:hover {
        transform: translateY(-2px);
    }

    .avva-description {
        font-size: 0.875rem;
        color: var(--avva-text-muted);
    }

    /* Zone d'affichage du QR Code avec effet de survole */
    .avva-qr-container {
        background-color: #f8fafc;
        border: 1px solid var(--avva-border-color);
        border-radius: var(--avva-radius-elem);
        padding: 1.25rem;
        text-align: center;
        margin-bottom: 1.25rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .avva-qr-container:hover {
        transform: scale(1.01);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
    }

    .avva-qr-frame {
        background: #ffffff;
        padding: 0.75rem;
        border-radius: 14px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--avva-border-color);
        margin-bottom: 0.75rem;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avva-qr-frame:hover {
        transform: scale(1.05);
    }

    .avva-qr-frame img {
        max-width: 160px;
        height: auto;
        display: block;
    }

    .avva-secret-box {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        align-items: center;
    }

    .avva-secret-label {
        font-size: 0.75rem;
        color: var(--avva-text-muted);
    }

    .avva-secret-code {
        font-family: monospace;
        font-size: 0.9rem;
        background-color: #e2e8f0;
        color: var(--avva-text-dark);
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        letter-spacing: 0.05em;
        user-select: all;
        transition: background-color 0.2s ease;
    }

    .avva-secret-code:hover {
        background-color: #cbd5e1;
    }

    /* Instructions */
    .avva-instructions-list {
        font-size: 0.8rem;
        color: var(--avva-text-muted);
        padding-left: 1.2rem;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .avva-instructions-list li {
        margin-bottom: 0.25rem;
    }

    /* Formulaire et Inputs */
    .avva-form {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .avva-field-group {
        display: flex;
        flex-direction: column;
    }

    .avva-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--avva-text-muted);
    }

    .avva-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .avva-input-icon {
        position: absolute;
        left: 1rem;
        color: #94a3b8;
        font-size: 1.1rem;
        pointer-events: none;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .avva-input {
        width: 100%;
        padding: 0.85rem 1rem 0.85rem 2.8rem;
        background-color: var(--avva-bg-input);
        border: 1.5px solid var(--avva-border-color);
        border-radius: var(--avva-radius-elem);
        font-size: 0.95rem;
        color: var(--avva-text-dark);
        outline: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .avva-input:focus {
        background-color: #ffffff;
        border-color: var(--avva-blue);
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15), 0 4px 12px rgba(0, 102, 204, 0.08);
        transform: scale(1.01);
    }

    .avva-input:focus+.avva-input-icon,
    .avva-input-wrapper:focus-within .avva-input-icon {
        color: var(--avva-blue);
        transform: scale(1.15);
    }

    .avva-input-totp {
        text-align: center;
        letter-spacing: 0.35em;
        font-weight: 700;
        font-size: 1.25rem;
        padding-left: 2.8rem;
    }

    .avva-badge-2fa {
        font-size: 0.65rem;
        background-color: rgba(0, 102, 204, 0.1);
        color: var(--avva-blue);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        animation: avvaPulseBadge 2s infinite;
    }

    /* Bouton Vert AVVA */
    .avva-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.9rem;
        margin-top: 0.5rem;
        border: none;
        border-radius: var(--avva-radius-elem);
        font-weight: 600;
        font-size: 1rem;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .avva-btn-success {
        background-color: var(--avva-green);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
    }

    .avva-btn-success:hover {
        background-color: var(--avva-green-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.35);
    }

    .avva-btn-icon {
        font-size: 1.25rem;
        transition: transform 0.3s ease;
    }

    .avva-btn-primary:hover .avva-btn-icon {
        transform: scale(1.25);
    }

    /* Pied de carte */
    .avva-card-footer {
        padding: 1.1rem 2rem;
        background-color: rgba(248, 250, 252, 0.8);
        border-top: 1px solid var(--avva-border-color);
        text-align: center;
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

    .avva-arrow-left {
        transition: transform 0.3s ease;
    }

    .avva-link:hover .avva-arrow-left {
        transform: translateX(-4px);
    }

    /* Alerts */
    .avva-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: var(--avva-radius-elem);
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
    }

    .avva-alert-danger {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* ==========================================================
   ANIMATIONS EN CASCADE (STAGGERED ANIMATIONS)
   ========================================================== */

    .avva-anim-1,
    .avva-anim-2,
    .avva-anim-3,
    .avva-anim-4,
    .avva-anim-5,
    .avva-anim-6 {
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

    .avva-anim-6 {
        animation-delay: 0.6s;
    }

    .avva-anim-shake {
        animation: avvaShake 0.5s ease-in-out forwards;
    }

    /* Keyframe Definitions */
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

    @keyframes avvaPulseBadge {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.8;
            transform: scale(0.96);
        }
    }

    @keyframes avvaShake {

        0%,
        100% {
            transform: translateX(0);
        }

        20%,
        60% {
            transform: translateX(-6px);
        }

        40%,
        80% {
            transform: translateX(6px);
        }
    }

    .avva-icon-pulse {
        display: inline-block;
        animation: avvaPulseBadge 2.5s infinite;
    }
</style>
<div class="avva-login-wrapper">
    <div class="avva-card avva-card-wide">

        <div class="avva-top-bar"></div>

        <div class="avva-card-body">

            <header class="avva-header avva-anim-1">
                <div class="avva-logo-box">
                    <img src="/assets/images/logo-avva39.png" alt="Logo AVVA39" class="avva-logo">
                </div>
                <h1 class="avva-title">Cartoguide</h1>
                <p class="avva-subtitle">Configuration 2FA (Étape 2/2)</p>
            </header>

            <div class="avva-step-badge avva-anim-2">
                <i class="bi bi-qr-code-scan text-avva-blue avva-icon-pulse"></i>
                <span>Association TOTP</span>
            </div>

            <div class="text-center mb-4 avva-anim-2">
                <h2 class="fw-bold fs-5 text-dark mb-1">
                    Bienvenue, <?= htmlspecialchars($user->getPrenom()) . ' ' . htmlspecialchars($user->getNom()) ?> !
                </h2>
                <p class="avva-description mb-0">
                    Scannez le QR code ci-dessous pour lier votre application de sécurité.
                </p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="avva-alert avva-alert-danger avva-anim-shake" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <strong>Erreur de vérification</strong><br>
                        <span><?= htmlspecialchars($error); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="avva-qr-container avva-anim-3">
                <?php if ($qrCodeUrl): ?>
                    <div class="avva-qr-frame">
                        <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code TOTP" class="img-fluid">
                    </div>

                    <div class="avva-secret-box">
                        <span class="avva-secret-label">Clé Secrète (si le scan échoue) :</span>
                        <code class="avva-secret-code"><?= htmlspecialchars($secret) ?></code>
                    </div>
                <?php else: ?>
                    <div class="avva-alert avva-alert-danger mb-0" role="alert">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                        <span>Impossible de générer le QR code. Veuillez contacter l'assistance.</span>
                    </div>
                <?php endif; ?>
            </div>

            <ol class="avva-instructions-list avva-anim-4">
                <li>Ouvrez votre application TOTP (Google Authenticator, Authy, etc.).</li>
                <li>Scannez le QR code ou saisissez la clé secrète.</li>
                <li>Entrez le code temporaire généré ci-dessous.</li>
            </ol>

            <form action="" method="POST" novalidate class="avva-form">

                <div class="avva-field-group avva-anim-5">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="totp_code" class="avva-label mb-0">Code de vérification</label>
                        <span class="avva-badge-2fa">6 Chiffres</span>
                    </div>
                    <div class="avva-input-wrapper">
                        <i class="bi bi-shield-check avva-input-icon"></i>
                        <input type="text" id="totp_code" name="totp_code" class="avva-input avva-input-totp"
                            inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000 000" required
                            autocomplete="one-time-code">
                    </div>
                </div>

                <button type="submit" class="avva-btn-primary avva-btn-success avva-anim-6">
                    <span>Activer la Double Authentification</span>
                    <i class="bi bi-check-lg avva-btn-icon"></i>
                </button>

            </form>

        </div>

        <footer class="avva-card-footer avva-anim-6">
            <a href="/cartoguide/connexion" class="avva-link">
                <i class="bi bi-arrow-left avva-arrow-left"></i> Annuler et retourner à la connexion
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