<?php
// On récupère la chaîne des musiques de l'article (ex: "music1.mp3,music2.mp3")
$musicString = $article['music_file'] ?? ''; 
$userPlaylist = [];

if (!empty($musicString)) {
    $files = explode(',', $musicString);
    foreach ($files as $file) {
        $file = trim($file);
        if (!empty($file)) {
            $userPlaylist[] = "../../uploads/" . $file;
        }
    }
}
$jsonPlaylist = json_encode($userPlaylist);
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Ce site permet d'avoir les informations d'astronomie et de météorologie et de contacter pour avoir des renseignement supplémentaire et bien d'autre">
    <meta name="keywords" content="">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="stylesheet" href="/css/style.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js">
    </script>
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>

</head>

<body>
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
            <source src="/__partials/Expedition-Long-Version-chosic.com_.mp3" type="audio/mpeg">
        </audio>
    </div>

    <style>
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
    </style>
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
                $version = require __DIR__ . '/version.php';
                $siteVersion = $version['siteVersion'];
                $appVersion  = $siteVersion . '.' . $version['appBuild'];
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
                                <?php include "counter.php"; ?> visiteurs
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
    <button class="pwa-about-trigger" onclick="document.getElementById('pwa-about-modal').showModal()" aria-label="Ouvrir les informations de la station">
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
            <button class="pwa-close-modal" onclick="document.getElementById('pwa-about-modal').close()" aria-label="Fermer"></button>
        </div>

        <header class="pwa-header-top">
            <div class="pwa-brand">
                <img src="/ressources/logo.png" alt="Meteastro Logo" class="pwa-logo">
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
                    <span class="pwa-meta-value"><?php include "counter.php"; ?></span>
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

                <a href="https://github.com/adrienb39/meteastro" target="_blank" rel="noopener" class="pwa-settings-item">
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
            <p class="pwa-legal-tagline">Station spatiale Meteastro — Astronomie & Météorologie en temps réel.</p>
            <p class="pwa-legal-credits">Conçu par <strong>Adrien Bruyère</strong> • Tous droits réservés © <?= date('Y') ?></p>
        </footer>

    </div>
</dialog>
</div>
<style>
/* ==========================================================================
   1. VARIABLES DE THÈME (STYLE FLOTTANT ANDROID MATERIAL YOU)
   ========================================================================== */
:root {
  /* --- Mode Clair (Couleurs de surface Android) --- */
  --bg-header: rgba(243, 243, 244, 0.90); /* Opacité 90% teintée Android 14 */
  --bg-nav: rgba(243, 243, 244, 0.88);    /* Transparence équilibrée native */
  --border-color: rgba(0, 0, 0, 0.06);    /* Délimitation subtile */
  --text-inactive: #444746;               /* Teinte "On-Surface Variant" Android */
  --text-active: #0b57d0;                 /* Bleu primaire standard Material 3 */
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
    --text-active: #a8c7fa;               /* Bleu clair adaptatif écran sombre */
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
  display: none !important; /* Masqué sur navigateur de bureau */
}

.container-mobile {
  padding-bottom: 30px; /* Espace standard de fin de page */
  transition: padding 0.25s ease;
}

/* --- CONTRÔLE D'AFFICHAGE DE L'INTERFACE DE L'APPLICATION --- */

/* 1. Caché par défaut sur tous les navigateurs web classiques */
.pwa-app-dashboard {
    display: none !important;
}

/* 2. Affiché uniquement si l'application est lancée en mode Standalone (PWA installée) */
@media (display-mode: standalone), (display-mode: fullscreen), (display-mode: minimal-ui) {
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
    border-radius: 24px; /* Format "pilule flottante" Material You */
    
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
    font-weight: 500; /* Medium font-weight natif Android */
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
    transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1); /* Courbe haptique Android */
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
    transform: scale(0.9); /* Feedback haptique visuel doux Android */
  }

  .nav-item.active {
    color: var(--text-active); 
    font-weight: 700; /* Met en valeur l'élément actif */
  }

  /* --- ADAPTATION STRUCTURELLE DE L'APPLICATION --- */
  .container-mobile {
    padding-bottom: 100px !important; 
    box-sizing: border-box;
  }

  .navbar, #playerContainer {
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
    bottom: 72px;            /* Placé idéalement juste au-dessus de la bottom-nav */
    
    /* CORRECTION ANGLE : Aligné sur la droite de l'onglet avec un retrait de sécurité */
    right: 0;
    margin-right: 8px;       
    width: 180px;
    
    /* Design & Couleurs adaptatives */
    background: var(--bg-nav);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-nav);
    border-radius: 20px;     /* Coins généreusement arrondis style Material 3 */
    padding: 6px;
    z-index: 1001;
    box-sizing: border-box;
    
    /* GESTION DE L'ANIMATION (Sans display: none pour préserver la fluidité) */
    visibility: hidden;
    opacity: 0;
    transform: translateY(12px) scale(0.96); /* Effet d'apparition du bas vers le haut */
    transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1), 
                opacity 0.22s ease, 
                visibility 0.22s ease;
}

/* ÉTAT ACTIF : Classe injectée par le JavaScript à l'ouverture */
.nav-submenu.open {
    visibility: visible;
    opacity: 1;
    transform: translateY(0) scale(1); /* Rendu stable et centré sur sa zone */
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
  .submenu-item { color: #e3e3e3; }
}

.submenu-item:hover, .submenu-item.active {
  background: rgba(0, 0, 0, 0.05);
}
@media (prefers-color-scheme: dark) {
  .submenu-item:hover, .submenu-item.active { background: rgba(255, 255, 255, 0.08); }
}

.submenu-icon { margin-right: 10px; font-size: 16px; }
.submenu-divider { border: 0; border-top: 1px solid rgba(0, 0, 0, 0.08); margin: 6px 0; }
@media (prefers-color-scheme: dark) { .submenu-divider { border-top-color: rgba(255, 255, 255, 0.1); } }

.logout-item { color: #dc3545; } /* Rouge pour la déconnexion */

/* Conteneur pour aligner le texte et la flèche sur la même ligne */
.nav-label-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px; /* Espace entre le texte et la flèche */
}

/* Style de la petite flèche */
.submenu-caret {
  font-size: 8px; /* Plus discret que le texte */
  color: #757575; /* Couleur grise par défaut */
  transition: transform 0.2s ease; /* Animation fluide de rotation */
  display: inline-block;
}

@media (prefers-color-scheme: dark) {
  .submenu-caret {
    color: #aaa;
  }
}

/* Changement de couleur au survol/actif de l'onglet parent */
.nav-item-has-submenu:hover .submenu-caret {
  color: #0b57d0; /* S'adapte à la couleur active de votre thème */
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
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(25, 135, 84, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
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
    margin-right: 4px; /* Espace léger avant le logo */
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
.sun-icon { color: #ffb703; }
.moon-icon { color: #a2d2ff; }

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
    bottom: 20px; /* Distance par rapport au bas de l'écran une fois "bloqué" */
    
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
    background: #0d1117; /* Mettre la couleur de fond sombre par défaut de votre app */
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 16px 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    margin: 0 auto;
}

.lightmode .pwa-about-dialog .pwa-app-dashboard {
    background: #ffffff; /* Mettre la couleur de fond claire par défaut de votre app */
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
</style>

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
    <script src="/js/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const music = document.getElementById('bgMusic');
            const btn = document.getElementById('audioToggle');
            const infoBtn = document.getElementById('infoToggle');
            const infoSlide = document.getElementById('extraInfo');
            const player = document.getElementById('playerContainer');
            const titleDisplay = document.getElementById('currentTrackTitle');

            // CONFIGURATION
            const defaultMusic = "/__partials/Expedition-Long-Version-chosic.com_.mp3";
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
</body>

</html>