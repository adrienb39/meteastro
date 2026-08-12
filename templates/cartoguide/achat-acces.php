<style>
    :root {
        --avva-blue: #0066cc;
        --avva-blue-dark: #004d99;
        --avva-green: #28a745;
        --avva-green-light: #e8f5e9;
        --avva-orange: #f59e0b;
        --avva-text-dark: #1e293b;
        --avva-text-muted: #64748b;
        --avva-bg-input: #f8fafc;
        --avva-border-color: #e2e8f0;
        --avva-radius-card: 28px;
        --avva-radius-elem: 14px;
    }

    /* ==========================================================
   1. CONTENEUR ET CARTE PRINCIPALE
   ========================================================== */
    .avva-login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: radial-gradient(circle at 50% 50%, rgba(0, 102, 204, 0.05) 0%, transparent 70%);
    }

    .avva-card {
        width: 100%;
        max-width: 640px;
        margin: 90px auto;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: var(--avva-radius-card);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.6) inset;
        overflow: hidden;
        position: relative;
        animation: avvaCardEnter 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
    }

    .avva-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 60px -12px rgba(0, 102, 204, 0.18), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
    }

    .avva-top-bar {
        height: 6px;
        width: 100%;
        background: linear-gradient(90deg, var(--avva-blue), var(--avva-green), var(--avva-orange), var(--avva-blue));
        background-size: 300% 100%;
        animation: avvaGradientShift 3s linear infinite;
    }

    .avva-card-body {
        padding: 2.25rem 2rem 1.75rem 2rem;
    }

    /* ==========================================================
   2. HEADER & LOGO
   ========================================================== */
    .avva-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .avva-logo {
        max-height: 80px;
        width: auto;
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.08));
    }

    .avva-logo:hover {
        transform: scale(1.1) rotate(-3deg);
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

    .avva-icon-pulse {
        display: inline-block;
        animation: avvaPulse 2s infinite ease-in-out;
    }

    /* ==========================================================
   3. FORMULES DE PRIX
   ========================================================== */
    .avva-plans-container {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        margin-bottom: 1.5rem;
    }

    .avva-plan-card {
        position: relative;
        cursor: pointer;
    }

    .avva-plan-card input[type="radio"] {
        display: none;
    }

    .avva-plan-content {
        padding: 1rem 1.25rem;
        border: 1.5px solid var(--avva-border-color);
        background-color: var(--avva-bg-input);
        border-radius: var(--avva-radius-elem);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .avva-plan-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .avva-plan-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--avva-text-dark);
    }

    .avva-plan-price {
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--avva-blue);
    }

    .avva-plan-price span {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--avva-text-muted);
    }

    .avva-plan-desc {
        font-size: 0.78rem;
        color: var(--avva-text-muted);
        margin: 0;
        padding-right: 1.5rem;
    }

    .avva-plan-icon {
        position: absolute;
        right: 1.25rem;
        bottom: 1rem;
        font-size: 1.1rem;
        color: var(--avva-border-color);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avva-plan-card:hover .avva-plan-content {
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .avva-plan-card input[type="radio"]:checked+.avva-plan-content {
        background-color: #ffffff;
        border-color: var(--avva-blue);
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.12), 0 8px 16px rgba(0, 102, 204, 0.08);
    }

    .avva-plan-card input[type="radio"]:checked+.avva-plan-content .avva-plan-icon {
        color: var(--avva-blue);
        transform: scale(1.2);
    }

    .avva-badge-pop {
        position: absolute;
        top: -9px;
        right: 15px;
        background: linear-gradient(135deg, var(--avva-orange), #d97706);
        color: #ffffff;
        font-size: 0.62rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 0.15rem 0.55rem;
        border-radius: 20px;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
    }

    /* ==========================================================
   4. GRILLE ET CHAMPS DU FORMULAIRE
   ========================================================== */
    .avva-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.85rem 1rem;
    }

    .avva-col-full {
        grid-column: span 2;
    }

    .avva-col-small {
        grid-column: span 1;
    }

    @media (max-width: 576px) {
        .avva-form-grid {
            grid-template-columns: 1fr;
        }

        .avva-col-full,
        .avva-col-small {
            grid-column: span 1;
        }
    }

    .avva-field-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 0.25rem;
    }

    .avva-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--avva-text-muted);
        margin-bottom: 0.35rem;
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
        font-size: 1rem;
        transition: color 0.3s ease, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avva-input,
    select.avva-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.6rem;
        background-color: var(--avva-bg-input);
        border: 1.5px solid var(--avva-border-color);
        border-radius: var(--avva-radius-elem);
        font-size: 0.9rem;
        color: var(--avva-text-dark);
        outline: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        appearance: none;
    }

    .avva-input:focus {
        background-color: #ffffff;
        border-color: var(--avva-blue);
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15);
        transform: translateY(-1px);
    }

    .avva-input:focus+.avva-input-icon,
    .avva-input-wrapper:focus-within .avva-input-icon {
        color: var(--avva-blue);
        transform: scale(1.15);
    }

    /* ==========================================================
   5. BOUTON & FOOTER
   ========================================================== */
    .avva-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.95rem;
        background: linear-gradient(135deg, var(--avva-blue), var(--avva-blue-dark));
        color: #ffffff;
        border: none;
        border-radius: var(--avva-radius-elem);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0, 102, 204, 0.25);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        margin-top: 1rem;
    }

    .avva-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(0, 102, 204, 0.38);
    }

    .avva-secure-badge {
        margin-top: 1.2rem;
        text-align: center;
        font-size: 0.75rem;
        color: var(--avva-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .avva-card-footer {
        padding: 1.1rem 2rem;
        background-color: rgba(248, 250, 252, 0.8);
        border-top: 1px solid var(--avva-border-color);
        text-align: center;
    }

    .avva-footer-text {
        font-size: 0.75rem;
        color: var(--avva-text-muted);
        margin-right: 0.35rem;
    }

    .avva-link {
        color: var(--avva-blue);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: color 0.2s ease;
    }

    .avva-link:hover {
        color: var(--avva-blue-dark);
    }

    /* ANIMATIONS KEYFRAMES */
    .avva-anim-1 {
        animation: avvaFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: 0.1s;
        opacity: 0;
    }

    .avva-anim-2 {
        animation: avvaFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: 0.2s;
        opacity: 0;
    }

    .avva-anim-3 {
        animation: avvaFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: 0.3s;
        opacity: 0;
    }

    .avva-anim-4 {
        animation: avvaFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: 0.4s;
        opacity: 0;
    }

    @keyframes avvaCardEnter {
        from {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes avvaFadeUp {
        from {
            opacity: 0;
            transform: translateY(18px);
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

    @keyframes avvaPulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.15);
            opacity: 0.8;
        }
    }
</style>

<div class="avva-login-wrapper">
    <div class="avva-card">

        <div class="avva-top-bar"></div>

        <div class="avva-card-body">

            <header class="avva-header avva-anim-1">
                <div class="avva-logo-box">
                    <img src="/assets/images/logo-avva39.png" alt="Logo AVVA39" class="avva-logo">
                </div>
                <h1 class="avva-title">S'abonner à Cartoguide</h1>
                <p class="avva-subtitle">
                    <i class="bi bi-cart-check-fill text-avva-blue avva-icon-pulse"></i> Choisissez la formule adaptée à
                    vos besoins
                </p>
            </header>

            <form action="" method="POST" novalidate class="avva-form">

                <div class="avva-plans-container avva-anim-2">
                    <label class="avva-plan-card">
                        <input type="radio" name="plan" value="monthly" checked>
                        <div class="avva-plan-content">
                            <div class="avva-plan-header">
                                <span class="avva-plan-title">Accès Mensuel</span>
                                <span class="avva-plan-price">9,99 € <span>/ mois</span></span>
                            </div>
                            <p class="avva-plan-desc">Sans engagement. Idéal pour une découverte ou un besoin ponctuel.
                            </p>
                            <i class="bi bi-check-circle-fill avva-plan-icon"></i>
                        </div>
                    </label>

                    <label class="avva-plan-card">
                        <span class="avva-badge-pop">Populaire</span>
                        <input type="radio" name="plan" value="yearly">
                        <div class="avva-plan-content">
                            <div class="avva-plan-header">
                                <span class="avva-plan-title">Pass Annuel</span>
                                <span class="avva-plan-price">79,99 € <span>/ an</span></span>
                            </div>
                            <p class="avva-plan-desc">Économisez plus de 30% par rapport au tarif mensuel.</p>
                            <i class="bi bi-check-circle-fill avva-plan-icon"></i>
                        </div>
                    </label>
                </div>

                <div class="avva-form-grid avva-anim-3">

                    <div class="avva-field-group avva-col-full">
                        <label for="numero_licence_membre" class="avva-label">
                            Numéro de licence <span class="text-muted text-lowercase fw-normal">(optionnel)</span>
                        </label>
                        <div class="avva-input-wrapper">
                            <input type="number" id="numero_licence_membre" name="numero_licence_membre"
                                class="avva-input" placeholder="Ex: 123456">
                            <i class="bi bi-card-heading avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="nom_membre" class="avva-label">Nom</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="nom_membre" name="nom_membre" class="avva-input" placeholder="Dupont"
                                maxlength="50" required>
                            <i class="bi bi-person-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="prenom_membre" class="avva-label">Prénom</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="prenom_membre" name="prenom_membre" class="avva-input"
                                placeholder="Jean" maxlength="50" required>
                            <i class="bi bi-person-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="date_naissance_membre" class="avva-label">Date de naissance</label>
                        <div class="avva-input-wrapper">
                            <input type="date" id="date_naissance_membre" name="date_naissance_membre"
                                class="avva-input" required>
                            <i class="bi bi-calendar-event-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="sexe_membre" class="avva-label">Sexe</label>
                        <div class="avva-input-wrapper">
                            <select id="sexe_membre" name="sexe_membre" class="avva-input" required>
                                <option value="" disabled selected>Sélectionner</option>
                                <option value="M">Homme</option>
                                <option value="F">Femme</option>
                                <option value="O">Autre</option>
                            </select>
                            <i class="bi bi-gender-ambiguous avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="numero_voie_membre" class="avva-label">N° de voie</label>
                        <div class="avva-input-wrapper">
                            <input type="number" id="numero_voie_membre" name="numero_voie_membre" class="avva-input"
                                placeholder="12" required>
                            <i class="bi bi-hash avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="nom_voie_membre" class="avva-label">Nom de voie</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="nom_voie_membre" name="nom_voie_membre" class="avva-input"
                                placeholder="Rue de la Paix" maxlength="50" required>
                            <i class="bi bi-geo-alt-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="code_postal_membre" class="avva-label">Code postal</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="code_postal_membre" name="code_postal_membre" class="avva-input"
                                placeholder="39000" maxlength="5" required>
                            <i class="bi bi-mailbox avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="ville_membre" class="avva-label">Ville</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="ville_membre" name="ville_membre" class="avva-input"
                                placeholder="Lons-le-Saunier" maxlength="50" required>
                            <i class="bi bi-building avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="numero_telephone_membre" class="avva-label">Téléphone</label>
                        <div class="avva-input-wrapper">
                            <input type="tel" id="numero_telephone_membre" name="numero_telephone_membre"
                                class="avva-input" placeholder="0612345678" maxlength="50" required>
                            <i class="bi bi-telephone-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-col-small">
                        <label for="email_membre" class="avva-label">Adresse Email</label>
                        <div class="avva-input-wrapper">
                            <input type="email" id="email_membre" name="email_membre" class="avva-input"
                                placeholder="jean.dupont@exemple.fr" maxlength="50" required>
                            <i class="bi bi-envelope-fill avva-input-icon"></i>
                        </div>
                    </div>

                </div>

                <button type="submit" class="avva-btn-primary avva-anim-4">
                    <span>Procéder au paiement sécurisé</span>
                    <i class="bi bi-credit-card-2-front-fill avva-btn-icon"></i>
                </button>

            </form>

            <div class="avva-secure-badge avva-anim-4">
                <i class="bi bi-shield-check text-avva-green"></i> Paiement 100% sécurisé via Stripe / Carte Bancaire
            </div>

        </div>

        <footer class="avva-card-footer avva-anim-4">
            <div class="d-flex justify-content-center align-items-center">
                <span class="avva-footer-text">Déjà un compte ou membre ?</span>
                <a href="/cartoguide/connexion" class="avva-link">
                    <i class="bi bi-box-arrow-in-right"></i> Retour à la connexion
                </a>
            </div>
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