<?php
/**
 * Vue de connexion.
 * Variables injectées par LoginController::login() via $this->render():
 * @var bool                $isConnected
 * @var array<string,string> $errors
 * @var string               $email
 */
$errors ??= [];
$email ??= '';
$isConnected ??= false;

$themeChoice = $_COOKIE['meteastro_theme'] ?? 'dark';
$bodyClass = ($themeChoice === 'light') ? 'lightmode' : '';
?>
<style>
    /* ==========================================================================
           1. VARIABLES DE THÈME (STYLE FLOTTANT ANDROID MATERIAL YOU)
           ========================================================================== */
    :root {
        /* --- Mode Clair (Couleurs de surface Android) --- */
        --bg-header: rgba(243, 243, 244, 0.90);
        --bg-nav: rgba(243, 243, 244, 0.88);
        --border-color: rgba(0, 0, 0, 0.06);
        --text-inactive: #444746;
        --text-active: #0b57d0;
        --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-nav: 0 2px 6px rgba(0, 0, 0, 0.08);
        --logout-color: #dc3545;
    }

    .lightmode {
        --bg-header: rgba(243, 243, 244, 0.90);
        --bg-nav: rgba(243, 243, 244, 0.88);
        --border-color: rgba(0, 0, 0, 0.06);
        --text-inactive: #444746;
        --text-active: #0b57d0;
        --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-nav: 0 2px 6px rgba(0, 0, 0, 0.08);
        --logout-color: #dc3545;
    }

    @media (prefers-color-scheme: dark) {
        :root {
            /* --- Mode Sombre (Couleurs de surface Dark Android) --- */
            --bg-header: rgba(31, 31, 31, 0.90);
            --bg-nav: rgba(31, 31, 31, 0.88);
            --border-color: rgba(255, 255, 255, 0.07);
            --text-inactive: #c4c7c5;
            --text-active: #a8c7fa;
            --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.2);
            --shadow-nav: 0 4px 12px rgba(0, 0, 0, 0.3);
            --logout-color: #ff4a5a;
        }
    }

    /* Forces l'application des variables si la classe lightmode est présente sur le body */
    body.lightmode {
        --bg-header: rgba(243, 243, 244, 0.90);
        --bg-nav: rgba(243, 243, 244, 0.88);
        --border-color: rgba(0, 0, 0, 0.06);
        --text-inactive: #444746;
        --text-active: #0b57d0;
        --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-nav: 0 2px 6px rgba(0, 0, 0, 0.08);
        --logout-color: #dc3545;
    }

    /* ==========================================================================
           2. GESTION PAR DÉFAUT (NAVIGATEURS WEB CLASSIQUES)
           ========================================================================== */
    .bottom-nav {
        display: none !important;
    }

    .container-mobile {
        padding-bottom: 30px;
        transition: padding 0.25s ease;
    }

    /* ==========================================================================
           3. COMPORTEMENT EXCLUSIF : MODE APP INSTALLÉE (STANDALONE)
           ========================================================================== */
    @media (display-mode: standalone) {

        .bottom-nav {
            display: flex !important;
            justify-content: space-around;
            position: fixed;
            bottom: 16px;
            left: 12px;
            right: 12px;
            height: 60px;
            background: var(--bg-nav);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-nav);
            border-radius: 24px;
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
            font-family: 'Outfit', sans-serif;
            font-size: 10px;
            font-weight: 500;
            height: 100%;
            flex: 1;
            padding: 4px 0;
            box-sizing: border-box;
            position: relative;
            cursor: pointer;
            user-select: none;
            transition: color 0.15s ease, font-weight 0.15s ease;
        }

        .nav-icon {
            font-size: 20px;
            margin-bottom: 3px;
            line-height: 1;
            transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1);
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

        .nav-label-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            max-width: 92%;
        }

        /* --- ÉTATS & RETOURS EFFETS TACTILES --- */
        .nav-item:active .nav-icon {
            transform: scale(0.9);
        }

        .nav-item.active,
        .nav-item:hover {
            color: var(--text-active);
            font-weight: 700;
        }

        /* --- SOUS-MENU CONFIGURATION COMPTE (MATERIAL YOU) --- */
        .nav-item-has-submenu {
            position: relative;
        }

        .nav-submenu {
            position: absolute;
            bottom: 72px;
            /* Espacement au-dessus de la pilule flottante */
            right: 50%;
            width: 180px;
            background: var(--bg-nav);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-nav);
            border-radius: 20px;
            /* Arrondi interne harmonieux */
            padding: 6px;
            z-index: 1001;
            box-sizing: border-box;

            /* Animation fluide sans display:none */
            visibility: hidden;
            opacity: 0;
            transform: translateX(50%) translateY(12px) scale(0.96);
            transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1), opacity 0.22s ease, visibility 0.22s ease;
        }

        /* Ouverture gérée par la classe injectée en JS */
        .nav-submenu.open {
            visibility: visible;
            opacity: 1;
            transform: translateX(50%) translateY(0) scale(1);
        }

        .submenu-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--text-main);
            font-size: 13px;
            font-weight: 500;
            border-radius: 14px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .submenu-item:hover,
        .submenu-item.active {
            background: rgba(11, 87, 208, 0.08);
            color: var(--text-active);
        }

        .submenu-icon {
            margin-right: 10px;
            font-size: 15px;
        }

        .submenu-divider {
            border: 0;
            border-top: 1px solid var(--border-color);
            margin: 6px 4px;
        }

        .logout-item {
            color: var(--logout-color);
        }

        .logout-item:hover {
            background: rgba(220, 53, 69, 0.08);
            color: var(--logout-color);
        }

        /* --- SYSTÈME DE CARET (FLÈCHE) AUTOMATISÉE --- */
        .submenu-caret {
            font-size: 8px;
            color: inherit;
            transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1);
            display: inline-block;
        }

        .nav-item-has-submenu.open .submenu-caret {
            transform: rotate(180deg);
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

        @media (max-width: 350px) {
            .nav-label {
                font-size: 9px;
            }

            .nav-icon {
                font-size: 18px;
            }
        }
    }
</style>
<div class="star-field" aria-hidden="true"></div>
<div class="glowing-stars" aria-hidden="true"></div>
<div class="planet" aria-hidden="true"></div>
<div class="asteroid" aria-hidden="true"></div>

<div class="forms-container">
    <div class="signin-signup">

        <?php if ($isConnected): ?>
            <section class="box glass-card animate-in">
                <div class="icon-header">
                    <i class="fa-solid fa-user-astronaut"></i>
                </div>
                <h2 class="title">COCKPIT ACTIF</h2>
                <p class="status-text">
                    Transmission stable. Vos identifiants sont déjà synchronisés avec la station.
                </p>
                <div class="action-group">
                    <a href="/" class="box-button secondary">RETOUR AU SITE</a>
                    <a href="/connexion/logout" class="box-button logout-btn">DÉCONNEXION</a>
                </div>
            </section>

        <?php else: ?>
            <form action="" method="POST" class="box glass-card animate-in" autocomplete="on" novalidate>
                <header>
                    <h2 class="title">CONNEXION</h2>
                    <p class="subtitle">Prêt pour le décollage ?</p>
                </header>

                <?php if (!empty($errors)): ?>
                    <div class="error-alert" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                if (isset($_SESSION['info'])) {
                    ?>
                    <div class="success-alert" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <?php echo $_SESSION['info']; ?>
                        </div>
                    </div>
                    <style>
                        .success-alert {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            background-color: rgba(25, 135, 84, 0.15);
                            border: 1px solid rgba(25, 135, 84, 0.4);
                            color: #2ecc71;
                            padding: 1rem;
                            border-radius: 12px;
                            margin-bottom: 1.5rem;
                        }

                        .success-alert i {
                            font-size: 1.25rem;
                            color: #2ecc71;
                        }
                    </style>
                    <?php
                }
                ?>

                <div class="input-group">
                    <div class="input-field">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        <input type="email" name="email" placeholder="Email"
                            value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>

                    <div class="input-field">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        <input type="password" name="password" placeholder="Mot de passe" required>
                    </div>
                </div>

                <div class="forgot-link">
                    <a href="forgot-password.php">Trajectoire perdue ? (Mdp oublié)</a>
                </div>

                <button type="submit" name="login" class="box-button btn-glow">
                    INITIALISER LA CONNEXION <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                </button>

                <footer class="form-footer">
                    <p>Pas encore de badge ?</p>
                    <a href="/connexion/signup" class="signup-link">REJOINDRE L'EXPÉDITION</a>
                </footer>

                <div class="home-return">
                    <a href="/">
                        <i class="fa-solid fa-house"></i> Retour Terre (Accueil)
                    </a>
                </div>
            </form>
        <?php endif; ?>

    </div>
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

    <?php if ($isConnected): ?>
        <a href="/connexion/contenu.php"
            class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'contenu.php') ? 'active' : ''; ?>">
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
                <a href="/connexion/profile.php"
                    class="submenu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                    <span class="submenu-icon">👤</span> Profil
                </a>
                <a href="#parametres/" class="submenu-item">
                    <span class="submenu-icon">⚙️</span> Paramètres
                </a>
                <hr class="submenu-divider">
                <a href="/connexion/logout.php" class="submenu-item logout-item">
                    <span class="submenu-icon">🚪</span> Déconnexion
                </a>
            </div>
        </div>
    <?php else: ?>
        <a href="/#contacts" data-anchor="contacts" class="nav-item">
            <span class="nav-icon">✉️</span>
            <span class="nav-label">Contact</span>
        </a>

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuTrigger = document.getElementById('account-menu-trigger');
        const submenu = document.getElementById('account-submenu');

        if (menuTrigger && submenu) {
            // Gestionnaire d'ouverture/fermeture tactile
            menuTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = submenu.classList.toggle('open');

                if (isOpen) {
                    menuTrigger.classList.add('open');
                } else {
                    menuTrigger.classList.remove('open');
                }
            });

            // Fermeture si clic en dehors du composant
            document.addEventListener('click', (e) => {
                if (!menuTrigger.contains(e.target)) {
                    submenu.classList.remove('open');
                    menuTrigger.classList.remove('open');
                }
            });
        }
    });
</script>