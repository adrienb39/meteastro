<?php
require_once "../config/controllerUserData.php";

/**
 * Détermination en temps réel de l'état de connexion de l'utilisateur
 */
$isConnected = isset($_SESSION['email'], $_SESSION['password']);

/**
 * Initialisation du thème : 
 * Le site est sombre par défaut. On n'ajoute 'lightmode' que si 
 * le cookie est explicitement défini sur 'light'.
 */
$themeChoice = $_COOKIE['meteastro_theme'] ?? 'dark';
$bodyClass = ($themeChoice === 'light') ? 'lightmode' : '';
?>
<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="<?php echo ($themeChoice === 'light') ? 'light' : 'dark'; ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription | Meteastro Expedition</title>
    
    <meta name="description" content="Rejoignez la station Meteastro. Créez votre compte pour accéder aux données astronomiques et météorologiques.">
    <link rel="icon" type="image/png" href="/ressources/logo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/connexion.css" />
    
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

            .nav-item.active, .nav-item:hover {
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
                right: 50%;
                width: 180px;
                background: var(--bg-nav);
                border: 1px solid var(--border-color);
                box-shadow: var(--shadow-nav);
                border-radius: 20px; 
                padding: 6px;
                z-index: 1001;
                box-sizing: border-box;
                
                visibility: hidden;
                opacity: 0;
                transform: translateX(50%) translateY(12px) scale(0.96);
                transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1), opacity 0.22s ease, visibility 0.22s ease;
            }

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

            .submenu-item:hover, .submenu-item.active {
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

            /* --- SYSTÈME DE CARET (FLÈCHE) --- */
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

            .navbar, #playerContainer {
                display: none !important;
            }

            @media (max-width: 350px) {
                .nav-label { font-size: 9px; }
                .nav-icon { font-size: 18px; }
            }
        }
    </style>
</head>

<body class="<?php echo $bodyClass; ?>">
    <div class="star-field" aria-hidden="true"></div>
    <div class="glowing-stars" aria-hidden="true"></div>
    <div class="planet" aria-hidden="true"></div>
    <div class="asteroid" aria-hidden="true"></div>

    <main class="container container-mobile">
        <div class="forms-container">
            <div class="signin-signup">

                <?php if ($isConnected): ?>
                    <section class="box glass-card animate-in">
                        <div class="icon-header">
                            <i class="fa-solid fa-user-astronaut"></i>
                        </div>
                        <h2 class="title">ACCÈS REFUSÉ</h2>
                        <p class="status-text">
                            Votre cockpit est déjà actif. Vous ne pouvez pas créer de nouveau compte en étant connecté.
                        </p>
                        <div class="action-group">
                            <a href="/index.php" class="box-button secondary">RETOUR AU BORD</a>
                            <a href="logout.php" class="box-button logout-btn">DÉCONNEXION</a>
                        </div>
                    </section>

                <?php else: ?>
                    <form action="signup.php" method="POST" class="box glass-card animate-in" autocomplete="off" novalidate>
                        <header>
                            <h2 class="title">REJOINDRE</h2>
                            <p class="subtitle">Créez votre badge d'explorateur</p>
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

                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-user" aria-hidden="true"></i>
                                <input type="text" name="name" placeholder="Nom complet" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <input type="email" name="email" placeholder="Email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                <input type="password" name="password" placeholder="Mot de passe" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                                <input type="password" name="cpassword" placeholder="Confirmer mot de passe" required>
                            </div>
                        </div>

                        <div class="consent-container">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="consent" id="consent" required>
                                <span class="checkmark"></span>
                                <span class="label-text">
                                    J'accepte les <a href="#" id="openTerms">Termes et Conditions</a>
                                </span>
                            </label>
                        </div>

                        <button type="submit" name="signup" class="box-button btn-glow">
                            CRÉER MON COMPTE <i class="fa-solid fa-rocket" aria-hidden="true"></i>
                        </button>

                        <footer class="form-footer">
                            <p>Déjà membre de l'équipage ?</p>
                            <a href="login.php" class="signup-link">CONNEXION AU COCKPIT</a>
                        </footer>

                        <div class="home-return">
                            <a href="/index.php">
                                <i class="fa-solid fa-house"></i> Retour Terre (Accueil)
                            </a>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <div id="termsModal" class="modal-overlay">
        <div class="modal-content glass-card animate-in">
            <div class="modal-header">
                <h2><i class="fa-solid fa-file-contract"></i> Termes et Conditions</h2>
                <button id="closeTerms" class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <h3>1. Acceptation des termes</h3>
                <p>En accédant à ce site, vous acceptez d'être lié par ces termes et conditions et toutes les lois et réglementations applicables.</p>
                
                <h3>2. Utilisation du site</h3>
                <p>Vous pouvez utiliser notre site uniquement à des fins légales et d'une manière qui ne porte pas atteinte aux droits des autres utilisateurs.</p>
                
                <h3>3. Propriété intellectuelle</h3>
                <p>Tous les contenus présents sur ce site (textes, graphiques, logos) sont la propriété de Meteastro.</p>
                
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

    <nav class="bottom-nav">
        <a href="/divers/astronomie/astronomie.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'astronomie.php') ? 'active' : ''; ?>">
            <span class="nav-icon">🪐</span>
            <span class="nav-label">Astro</span>
        </a>
        
        <a href="/divers/meteorologie/meteorologie.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'meteorologie.php') ? 'active' : ''; ?>">
            <span class="nav-icon">⛈️</span>
            <span class="nav-label">Météo</span>
        </a>

        <?php if ($isConnected): ?>
            <a href="/connexion/contenu.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'contenu.php') ? 'active' : ''; ?>">
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
                    <a href="/connexion/profile.php" class="submenu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
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

            <a href="/connexion/login.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">
                <span class="nav-icon">👤</span>
                <span class="nav-label">Connexion</span>
            </a>

            <a href="/connexion/signup.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'signup.php') ? 'active' : ''; ?>">
                <span class="nav-icon">👤➕</span>
                <span class="nav-label">S'inscrire</span>
            </a>
        <?php endif; ?>
    </nav>

    <script src="/js/login.js"></script>
    
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
</body>
</html>