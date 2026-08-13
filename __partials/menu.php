<?php
session_start();
require_once 'db.class.php';

// Initialisation DB
$obj = new Db();
// Authentification simplifiée
$isConnected = isset($_SESSION['email']) && isset($_SESSION['password']);

// --- LOGIQUE DE MISE À JOUR ---
if ($isConnected && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_update'])) {
  $newName = $_POST['name'];
  $newEmail = $_POST['email'];
  $newPass = $_POST['password'];
  $userId = $_SESSION['user_id']; // Assure-toi d'avoir l'ID en session

  // Mise à jour simple (à adapter selon ta structure de table 'users')
  // Remplace ton bloc de mise à jour par celui-ci :
  if (!empty($newPass)) {
    $hashedPass = password_hash($newPass, PASSWORD_BCRYPT);
    // On protège les noms de colonnes avec ` `
    $sql = "UPDATE `users` SET `name` = ?, `email` = ?, `password` = ? WHERE `id_users` = ?";
    $obj->query2($sql, [$newName, $newEmail, $hashedPass, $userId]);
  } else {
    $sql = "UPDATE `users` SET `name` = ?, `email` = ? WHERE `id_users` = ?";
    $obj->query2($sql, [$newName, $newEmail, $userId]);
  }

  // Déconnexion forcée après modification
  session_destroy();
  header("Location: connexion/login.php");
  exit();
}

$userName = $isConnected ? $_SESSION['name'] : '';
$userEmail = $isConnected ? $_SESSION['email'] : '';

$menuTable = $isConnected ? 'menu_connect' : 'menu_principal';
$menuItems = $obj->query("SELECT * FROM $menuTable ORDER BY parent ASC, id ASC");

/**
 * Fonction récursive pour générer le menu Bootstrap
 */
function renderBootstrapMenu($items, $parentId = 0)
{
  foreach ($items as $item) {
    if ($item['parent'] == $parentId) {
      $hasChildren = false;
      foreach ($items as $sub) {
        if ($sub['parent'] == $item['id']) {
          $hasChildren = true;
          break;
        }
      }

      if ($hasChildren) {
        echo '<li class="nav-item dropdown">';
        echo '<a class="nav-link dropdown-toggle ' . ($item['class'] ?? '') . '" href="' . $item['url'] . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . ucfirst($item['menu_name']) . '</a>';
        echo '<ul class="dropdown-menu shadow border-0 animate slideIn">';
        renderBootstrapMenu($items, $item['id']);
        echo '</ul></li>';
      } else {
        // Si c'est un enfant, on utilise dropdown-item, sinon nav-link
        $class = ($parentId == 0) ? 'nav-link' : 'dropdown-item';
        echo '<li><a class="' . $class . ' ' . ($item['class'] ?? '') . '" href="' . $item['url'] . '">' . ucfirst($item['menu_name']) . '</a></li>';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Meteastro - Votre portail d'astronomie et de météorologie.">
  <title>Meteastro | Astronomie & Météorologie</title>

  <link rel="icon" type="image/png" href="/ressources/logo.png">

  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    :root {
      --glass-bg: rgba(15, 23, 42, 0.8);
      --glass-border: rgba(255, 255, 255, 0.1);
      --accent-color: #3b82f6;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: #05070a;
      color: #e2e8f0;
      overflow-x: hidden;
    }

    /* SLIDE PARAMETRES */
    .edit-full-page {
      position: fixed;
      top: 0;
      left: 100%;
      width: 100%;
      height: 100%;
      background: #020617;
      z-index: 10000;
      overflow-y: auto;
      visibility: hidden;
      transition: left 0.5s cubic-bezier(0.77, 0, 0.175, 1), visibility 0.5s;
    }

    .edit-full-page.active {
      left: 0;
      visibility: visible;
    }

    .glass-container {
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(12px);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      padding: 2.5rem;
    }

    .input-glass {
      width: 100%;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      color: white;
      padding: 12px;
      border-radius: 12px;
      margin-top: 5px;
      outline: none;
    }

    .input-glass:focus {
      border-color: var(--accent-color);
      background: rgba(255, 255, 255, 0.1);
    }

    .btn-cosmic-glass {
      background: rgba(59, 130, 246, 0.2);
      border: 1px solid var(--accent-color);
      color: white;
      padding: 15px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s;
      width: 100%;
      cursor: pointer;
    }

    .btn-cosmic-glass:hover {
      background: var(--accent-color);
      transform: translateY(-2px);
    }

    /* Header & Logo */
    .header-top {
      background: linear-gradient(to right, #0f172a, #020617);
      padding: 1rem 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo-text {
      font-weight: 700;
      letter-spacing: 2px;
      color: white;
      text-decoration: none;
      font-size: 1.5rem;
    }

    /* Marquee moderne */
    .info-bar {
      background: rgba(59, 130, 246, 0.1);
      color: var(--accent-color);
      padding: 5px 0;
      font-size: 0.85rem;
      border-bottom: 1px solid rgba(59, 130, 246, 0.2);
    }

    /* Navbar Custom */
    .navbar {
      background: var(--glass-bg) !important;
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-link {
      font-weight: 400;
      transition: color 0.3s;
      margin: 0 5px;
    }

    .nav-link:hover {
      color: var(--accent-color) !important;
    }

    .dropdown-menu {
      background: #1e293b;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown-item:hover {
      background: var(--accent-color);
      color: white;
    }

    /* Protection Images */
    img {
      user-select: none;
      -webkit-user-drag: none;

      /* Modal Custom */
      .modal-content {
        background: #0f172a;
        border: 1px solid var(--glass-border);
        border-radius: 20px;
      }
  </style>
</head>

<body>
  <?php if ($isConnected): ?>
    <div id="settings-overlay" class="edit-full-page">
      <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
          <a href="#" class="btn btn-outline-light rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i> Retour
          </a>
          <h2 class="text-white mb-0 fw-600">Paramètres du Compte</h2>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-7">
            <div class="glass-container text-start shadow-lg">
              <form id="settingsForm" method="POST">
                <div class="mb-4">
                  <label class="text-white-50 small fw-bold">NOM D'UTILISATEUR</label>
                  <input type="text" name="name" class="input-glass" value="<?= htmlspecialchars($userName) ?>" required>
                </div>
                <div class="mb-4">
                  <label class="text-white-50 small fw-bold">ADRESSE EMAIL</label>
                  <input type="email" name="email" class="input-glass" value="<?= htmlspecialchars($userEmail) ?>"
                    required>
                </div>
                <div class="mb-4">
                  <label class="text-white-50 small fw-bold">NOUVEAU MOT DE PASSE</label>
                  <input type="password" name="password" class="input-glass" placeholder="••••••••">
                  <small class="text-white-50">Laissez vide pour conserver l'actuel.</small>
                </div>

                <button type="button" class="btn-cosmic-glass" data-bs-toggle="modal" data-bs-target="#confirmModal">
                  <i class="fa-solid fa-rotate me-2"></i> Synchroniser les données
                </button>

                <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg">
                      <div class="modal-body text-center p-5">
                        <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 3rem;"></i>
                        <h4 class="text-white mb-3">Attention !</h4>
                        <p class="text-white-50 mb-4">La modification de vos informations système entraînera une
                          <b>déconnexion immédiate</b> pour synchroniser votre terminal.
                        </p>
                        <div class="d-grid gap-2">
                          <button type="submit" name="confirm_update"
                            class="btn btn-primary rounded-pill py-2 fw-bold">Confirmer et se déconnecter</button>
                          <button type="button" class="btn btn-link text-white-50"
                            data-bs-dismiss="modal">Annuler</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="info-bar text-center">
    <div class="container">
      <i class="fas fa-satellite me-2"></i> Bienvenue sur Meteastro ! Système opérationnel.
    </div>
  </div>

  <header class="header-top">
    <div class="container d-flex justify-content-center align-items-center">
      <a href="/" class="logo-text d-flex justify-content-between align-items-center">
        <img src="/ressources/logo.png" alt="logo" width="40" class="me-3">
        METEASTRO
        <?php echo $isConnected ? '<span class="text-primary ms-2 fs-6">| ' . htmlspecialchars($userName) . '</span>' : ''; ?>
      </a>
    </div>
  </header>

<div class="pwa-app">
  <div class="pwa-status-bar">
        <span class="pulse-indicator"></span>
        <p><i class="fas fa-satellite"></i> Liaison Meteastro : Système opérationnel</p>
    </div>

    <header class="pwa-header-top">
        <div class="pwa-brand">
            <a href="/" class="pwa-back-button" aria-label="Retour à l'accueil">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
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
</div>

  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <?php renderBootstrapMenu($menuItems); ?>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container my-5">
  </main>

  <div class="container-mobile">
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

  <a href="/#contacts" data-anchor="contacts" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && (strpos($_SERVER['REQUEST_URI'], '#contacts') !== false || isset($_GET['contact']))) ? 'active' : ''; ?>">
    <span class="nav-icon">✉️</span>
    <span class="nav-label">Contact</span>
  </a>
  
  <?php if (isset($isConnected) && $isConnected === true): ?>
    <a href="/redirect.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'redirect.php') ? 'active' : ''; ?>">
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
    <a href="#parametres/" class="submenu-item <?php echo (basename($_SERVER['PHP_SELF']) == '#parametres/') ? 'active' : ''; ?>">
      <span class="submenu-icon">⚙️</span> Paramètres
    </a>
    <hr class="submenu-divider">
    <a href="/connexion/logout.php" class="submenu-item logout-item">
      <span class="submenu-icon">🚪</span> Déconnexion
    </a>
  </div>
</div>
  <?php else: ?>
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
<style>
/* ==========================================================================
   1. VARIABLES DE THÈME (STYLE FLOTTANT ANDROID MATERIAL YOU)
   ========================================================================== */
:root {
  /* --- Mode Clair (Couleurs de surface Android) --- */
  --bg-header: rgba(243, 243, 244, 0.90); /* Opacité 90% teintée Android 14 */
  --bg-nav: rgba(243, 243, 244, 0.88);    /* Transparence équilibrée native */
  --border-color: rgba(0, 0, 0, 0.06);    /* Délimitation subtile */
  --text-main: #1f1f1f;
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
    --text-main: #e3e3e3;
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

.pwa-app {
  display: none;
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

.pwa-app {
  display: block;
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuTrigger = document.getElementById('account-menu-trigger');
        const submenu = document.getElementById('account-submenu');

        if (menuTrigger && submenu) {
            // Gestionnaire d'ouverture/fermeture tactile
            menuTrigger.addEventListener('click', (e) => {
                // IMPORTANT : Si on clique à l'intérieur du sous-menu, on laisse le comportement normal (le lien fonctionne)
                if (submenu.contains(e.target)) {
                    return;
                }
                
                // Sinon, c'est qu'on a cliqué sur le bouton principal "Mon Compte", on bascule l'affichage
                e.stopPropagation();
                const isOpen = submenu.classList.toggle('open');
                
                if (isOpen) {
                    menuTrigger.classList.add('open');
                } else {
                    menuTrigger.classList.remove('open');
                }
            });

            // Fermeture si clic n'importe où en dehors du composant complet
            document.addEventListener('click', (e) => {
                if (!menuTrigger.contains(e.target)) {
                    submenu.classList.remove('open');
                    menuTrigger.classList.remove('open');
                }
            });
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gestion de la version de l'application
    const appVersion = "<?= $appVersion ?>"; // Calqué sur le CACHE_NAME du SW
    const versionField = document.getElementById('pwa-version');
    if (versionField) {
        versionField.textContent = `v${appVersion}`;
    }

    // 2. Détection de l'état du Service Worker de la PWA
    const swField = document.getElementById('pwa-sw');
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then((registration) => {
            if (swField) {
                if (registration.active) {
                    swField.textContent = "Actif";
                    swField.style.color = "#198754"; // Vert de confirmation système Android
                } else {
                    swField.textContent = "En attente";
                }
            }
        }).catch(() => {
            if (swField) swField.textContent = "Erreur SW";
        });
    } else {
        if (swField) swField.textContent = "Indisponible";
    }

    // 3. Calcul de l'espace de stockage alloué au cache de l'application
    const cacheField = document.getElementById('pwa-cache');
    if ('storage' in navigator && 'estimate' in navigator.storage) {
        navigator.storage.estimate().then((estimate) => {
            if (cacheField) {
                // Conversion précise en Mégaoctets (Mo)
                const usedSize = (estimate.usage / (1024 * 1024)).toFixed(2);
                cacheField.textContent = `${usedSize} Mo`;
            }
        }).catch(() => {
            if (cacheField) cacheField.textContent = "Erreur";
        });
    } else {
        if (cacheField) cacheField.textContent = "Non géré";
    }
});
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Protection globale des images
    document.addEventListener('contextmenu', e => {
      if (e.target.tagName === 'IMG') e.preventDefault();
    });
    document.addEventListener('dragstart', e => {
      if (e.target.tagName === 'IMG') e.preventDefault();
    });

    // Gestion du Slide
    function handleNavigation() {
      const overlay = document.getElementById('settings-overlay');
      if (window.location.hash === '#parametres/') {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
      } else {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    }
    window.addEventListener('hashchange', handleNavigation);
    window.addEventListener('load', handleNavigation);

    // Protection images
    document.addEventListener('contextmenu', e => { if (e.target.tagName === 'IMG') e.preventDefault(); });
  </script>
</body>

</html>