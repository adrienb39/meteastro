<style>
    :root {
        --avva-blue: #0066cc;
        --avva-blue-dark: #004d99;
        --avva-green: #28a745;
        --avva-green-light: #e8f5e9;
        --avva-orange: #f59e0b;
        --avva-orange-light: #fffbe8;
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
        max-width: 480px;
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
        transform: translateY(-6px);
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
        padding: 2.25rem 2rem 1.5rem 2rem;
    }

    /* ==========================================================
       2. HEADER & LOGO
       ========================================================== */
    .avva-header {
        text-align: center;
        margin-bottom: 1.25rem;
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
       3. TOGGLE / SWITCH DYNAMIQUE
       ========================================================== */
    .avva-pricing-toggle-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .avva-pricing-toggle-container input[type="radio"] {
        display: none;
    }

    .avva-toggle-wrapper {
        position: relative;
        display: flex;
        width: 100%;
        background-color: var(--avva-bg-input);
        border: 1px solid var(--avva-border-color);
        border-radius: 50px;
        padding: 4px;
        user-select: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .avva-toggle-wrapper:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .avva-toggle-labels {
        position: relative;
        z-index: 2;
        display: flex;
        width: 100%;
    }

    .avva-toggle-option {
        flex: 1;
        text-align: center;
        padding: 0.65rem 0.2rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--avva-text-muted);
        cursor: pointer;
        transition: color 0.35s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }

    .avva-toggle-option i {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avva-toggle-option:hover i {
        transform: scale(1.2);
    }

    .avva-toggle-slider {
        position: absolute;
        top: 4px;
        left: 4px;
        width: calc(33.333% - 4px);
        height: calc(100% - 8px);
        border-radius: 50px;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        z-index: 1;
    }

    /* ÉTATS SÉLECTIONNÉS */
    #toggle-member:checked~.avva-toggle-wrapper .option-member {
        color: #15803d;
    }

    #toggle-member:checked~.avva-toggle-wrapper .avva-toggle-slider {
        transform: translateX(0%);
        background-color: var(--avva-green-light);
        border: 1px solid rgba(40, 167, 69, 0.3);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
    }

    #toggle-non-member:checked~.avva-toggle-wrapper .option-non-member {
        color: var(--avva-blue);
    }

    #toggle-non-member:checked~.avva-toggle-wrapper .avva-toggle-slider {
        transform: translateX(100%);
        background-color: #ffffff;
        border: 1px solid var(--avva-border-color);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.12);
    }

    #toggle-trial:checked~.avva-toggle-wrapper .option-trial {
        color: #d97706;
    }

    #toggle-trial:checked~.avva-toggle-wrapper .avva-toggle-slider {
        transform: translateX(200%);
        background-color: var(--avva-orange-light);
        border: 1px solid rgba(245, 158, 11, 0.3);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }

    /* BADGES ET INFORMATIONS DYNAMIQUES */
    .avva-price-badge-info {
        margin: 0.85rem 0 1.25rem 0;
        font-size: 0.75rem;
        height: 1.2rem;
        position: relative;
        width: 100%;
        text-align: center;
    }

    .avva-price-info {
        transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        opacity: 0;
        pointer-events: none;
        position: absolute;
        left: 50%;
        transform: translateX(-50%) translateY(8px) scale(0.95);
        white-space: nowrap;
    }

    #toggle-member:checked~.avva-price-badge-info .info-member,
    #toggle-non-member:checked~.avva-price-badge-info .info-non-member,
    #toggle-trial:checked~.avva-price-badge-info .info-trial {
        opacity: 1;
        transform: translateX(-50%) translateY(0) scale(1);
    }

    #toggle-member:checked~.avva-price-badge-info .info-member {
        color: #15803d;
    }

    #toggle-non-member:checked~.avva-price-badge-info .info-non-member {
        color: var(--avva-blue);
    }

    #toggle-trial:checked~.avva-price-badge-info .info-trial {
        color: #d97706;
    }

    /* ==========================================================
       4. GESTION DYNAMIQUE DES CHAMPS (VISIBILITÉ)
       ========================================================== */
    .avva-field-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 1.1rem;
        animation: avvaFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Masquage des champs spécifiques */
    #toggle-member:checked~.avva-field-non-member-only,
    #toggle-member:checked~.avva-field-trial-only,
    #toggle-member:checked~.avva-btn-primary .avva-btn-text-non-member,
    #toggle-member:checked~.avva-btn-primary .avva-btn-text-trial {
        display: none !important;
    }

    #toggle-non-member:checked~.avva-field-member-only,
    #toggle-non-member:checked~.avva-field-trial-only,
    #toggle-non-member:checked~.avva-btn-primary .avva-btn-text-member,
    #toggle-non-member:checked~.avva-btn-primary .avva-btn-text-trial {
        display: none !important;
    }

    #toggle-trial:checked~.avva-field-member-only,
    #toggle-trial:checked~.avva-field-non-member-only,
    #toggle-trial:checked~.avva-field-totp,
    #toggle-trial:checked~.avva-btn-primary .avva-btn-text-member,
    #toggle-trial:checked~.avva-btn-primary .avva-btn-text-non-member {
        display: none !important;
    }

    .avva-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--avva-text-muted);
        margin-bottom: 0.4rem;
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
        transition: all 0.3s ease;
    }

    .avva-input:focus {
        background-color: #ffffff;
        border-color: var(--avva-blue);
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15), 0 4px 12px rgba(0, 102, 204, 0.08);
        transform: translateY(-1px);
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
    }

    .avva-badge-2fa {
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        background-color: rgba(0, 102, 204, 0.1);
        color: var(--avva-blue);
    }

    .avva-help-text {
        font-size: 0.75rem;
        color: var(--avva-text-muted);
        margin-top: 0.4rem;
        text-align: center;
    }

    .avva-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.1rem;
        border-radius: var(--avva-radius-elem);
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 1.25rem;
    }

    .avva-alert-success {
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .avva-alert-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* ==========================================================
       5. BOUTON PRINCIPAL
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
    }

    .avva-btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: left 0.6s ease;
    }

    .avva-btn-primary:hover::before {
        left: 100%;
    }

    .avva-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(0, 102, 204, 0.38);
    }

    .avva-btn-primary:active {
        transform: translateY(0) scale(0.98);
    }

    .avva-btn-icon {
        font-size: 1.25rem;
        transition: transform 0.3s ease;
    }

    .avva-btn-primary:hover .avva-btn-icon {
        transform: translateX(6px);
    }

    /* ==========================================================
       6. PIED DE CARTE
       ========================================================== */
    .avva-card-footer {
        padding: 1.1rem 2rem;
        background-color: rgba(248, 250, 252, 0.8);
        border-top: 1px solid var(--avva-border-color);
        text-align: center;
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
        gap: 0.35rem;
        transition: color 0.2s ease;
    }

    .avva-link:hover {
        color: var(--avva-blue-dark);
    }

    .avva-gear-spin {
        transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avva-link:hover .avva-gear-spin {
        transform: rotate(180deg);
    }

    .avva-login-wrapper:has(#toggle-member:checked) .avva-footer-non-member,
    .avva-login-wrapper:has(#toggle-member:checked) .avva-footer-trial {
        display: none !important;
    }

    .avva-login-wrapper:has(#toggle-non-member:checked) .avva-footer-member,
    .avva-login-wrapper:has(#toggle-non-member:checked) .avva-footer-trial {
        display: none !important;
    }

    .avva-login-wrapper:has(#toggle-trial:checked) .avva-footer-member,
    .avva-login-wrapper:has(#toggle-trial:checked) .avva-footer-non-member {
        display: none !important;
    }

    /* ==========================================================
       7. ANIMATIONS
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

    @keyframes avvaFadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
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
                <h1 class="avva-title">Cartoguide</h1>
                <p class="avva-subtitle">
                    <i class="bi bi-shield-lock-fill text-avva-blue avva-icon-pulse"></i> Connexion au service
                </p>
            </header>

            <form action="" method="POST" novalidate class="avva-form">

                <div class="avva-pricing-toggle-container avva-anim-2">
                    <input type="radio" name="membership_type" id="toggle-member" value="member" checked>
                    <!-- <input type="radio" name="membership_type" id="toggle-non-member" value="non-member"> -->
                    <input type="radio" name="membership_type" id="toggle-trial" value="trial">

                    <div class="avva-toggle-wrapper">
                        <div class="avva-toggle-labels">
                            <label for="toggle-member" class="avva-toggle-option option-member">
                                <i class="bi bi-shield-check"></i> Membre
                            </label>
                            <input type="radio" id="toggle-non-member" name="toggle-option" disabled>

                            <label class="avva-toggle-option option-non-member disabled-toggle">
                                <i class="bi bi-person"></i> Non-membre
                            </label>
                            <style>
                                /* Désactivation des interactions CSS */
                                .disabled-toggle {
                                    pointer-events: none;
                                    /* Empêche tout clic ou survol */
                                    opacity: 0.5;
                                    /* Rendu visuel grisé */
                                    cursor: not-allowed;
                                }
                            </style>
                            <script>
                                const inputNonMember = document.getElementById('toggle-non-member');

                                // Réinitialise la valeur si quelqu'un essaie de la cocher
                                inputNonMember.addEventListener('change', (event) => {
                                    event.preventDefault();
                                    inputNonMember.checked = false;
                                    console.warn("Cette option est verrouillée.");
                                });
                            </script>
                            <!-- <label for="toggle-non-member" class="avva-toggle-option option-non-member">
                                <i class="bi bi-person"></i> Non-membre
                            </label> -->
                            <label for="toggle-trial" class="avva-toggle-option option-trial">
                                <i class="bi bi-clock-history"></i> Essai
                                <!-- 7j -->
                            </label>
                        </div>
                        <div class="avva-toggle-slider"></div>
                    </div>

                    <div class="avva-price-badge-info">
                        <span class="avva-price-info info-member">
                            <i class="bi bi-check-circle-fill"></i> Accès <strong>Gratuit</strong> avec licence AVVA39
                        </span>
                        <span class="avva-price-info info-non-member">
                            <i class="bi bi-credit-card-fill"></i> Accès <strong>Abonné</strong> (Email ou Code)
                        </span>
                        <span class="avva-price-info info-trial">
                            <i class="bi bi-gift-fill"></i> Accès gratuit immédiat
                            <!-- pendant <strong>7 jours</strong> -->
                        </span>
                    </div>

                    <div class="avva-field-group avva-field-member-only avva-anim-3">
                        <label for="numeroLicence" class="avva-label">Numéro de Licence</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="numeroLicence" name="numeroLicence" class="avva-input"
                                placeholder="Ex: 123456" autocomplete="username">
                            <i class="bi bi-card-heading avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-field-non-member-only avva-anim-3">
                        <label for="identifiant_non_membre" class="avva-label">Email ou Code d'accès</label>
                        <div class="avva-input-wrapper">
                            <input type="text" id="identifiant_non_membre" name="identifiant" class="avva-input"
                                placeholder="Ex: client@exemple.fr ou CG-A1B2C3" autocomplete="username">
                            <i class="bi bi-envelope-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-field-trial-only avva-anim-3">
                        <label for="email_trial" class="avva-label">Votre Adresse Email</label>
                        <div class="avva-input-wrapper">
                            <input type="email" id="email_trial" name="email_trial" class="avva-input"
                                placeholder="exemple@domaine.fr" autocomplete="email">
                            <i class="bi bi-envelope-check-fill avva-input-icon"></i>
                        </div>
                    </div>

                    <div class="avva-field-group avva-field-totp avva-anim-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="totp_code" class="avva-label mb-0">Code de Sécurité TOTP</label>
                            <span class="avva-badge-2fa avva-icon-pulse">2FA Requis</span>
                        </div>
                        <div class="avva-input-wrapper">
                            <input type="text" id="totp_code" name="totp_code" class="avva-input avva-input-totp"
                                inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000 000"
                                autocomplete="one-time-code">
                            <i class="bi bi-key-fill avva-input-icon"></i>
                        </div>
                        <span class="avva-help-text">Entrez le code à 6 chiffres de votre application
                            Authenticator.</span>
                    </div>

                    <button type="submit" class="avva-btn-primary avva-anim-5 mt-3">
                        <span class="avva-btn-text-member">Se connecter</span>
                        <span class="avva-btn-text-non-member">Se connecter (Abonné)</span>
                        <span class="avva-btn-text-trial">Lancer mon essai gratuit</span>
                        <i class="bi bi-arrow-right-short avva-btn-icon"></i>
                    </button>

                </div>

            </form>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="avva-alert avva-alert-success avva-anim-pop" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($_SESSION['success_message']); ?></span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="avva-alert avva-alert-danger avva-anim-shake" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

        </div>

        <footer class="avva-card-footer avva-anim-6">
            <div class="avva-card-footer-content">
                <div class="avva-footer-member d-flex justify-content-center align-items-center">
                    <span class="avva-footer-text">Première connexion ?</span>
                    <a href="/cartoguide/premiere-configuration" class="avva-link">
                        <i class="bi bi-gear-wide-connected avva-gear-spin"></i> Configurer mon accès 2FA
                    </a>
                </div>

                <div class="avva-footer-non-member d-flex justify-content-center align-items-center">
                    <span class="avva-footer-text">Pas encore d'abonnement ?</span>
                    <a href="/cartoguide/achat-acces" class="avva-link">
                        <i class="bi bi-cart-check-fill"></i> Découvrir & Acheter un accès
                    </a>
                </div>
            </div>
        </footer>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleMember = document.getElementById('toggle-member');
        const toggleNonMember = document.getElementById('toggle-non-member');
        const toggleTrial = document.getElementById('toggle-trial');

        const inputLicence = document.getElementById('numeroLicence');
        const inputIdentifiant = document.getElementById('identifiant_non_membre');
        const inputTrial = document.getElementById('email_trial');
        const inputTotp = document.getElementById('totp_code');

        function updateInputs() {
            if (toggleMember.checked) {
                inputLicence.disabled = false;
                inputIdentifiant.disabled = true;
                inputTrial.disabled = true;

                inputTotp.disabled = false;
                inputTotp.required = true;
            } else if (toggleNonMember.checked) {
                inputLicence.disabled = true;
                inputIdentifiant.disabled = false;
                inputTrial.disabled = true;

                inputTotp.disabled = false;
                inputTotp.required = true;
            } else if (toggleTrial.checked) {
                inputLicence.disabled = true;
                inputIdentifiant.disabled = true;
                inputTrial.disabled = false;

                // Désactivation complète du TOTP en mode essai
                inputTotp.disabled = true;
                inputTotp.required = false;
            }
        }

        toggleMember.addEventListener('change', updateInputs);
        toggleNonMember.addEventListener('change', updateInputs);
        toggleTrial.addEventListener('change', updateInputs);

        updateInputs();
    });
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