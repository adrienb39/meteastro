<?php
session_start();
require_once 'db.class.php';

// Initialisation DB
$obj = new Db();

// Vérification de la connexion
$userId = $_SESSION['user_id'] ?? null;
$isConnected = !empty($userId);

// Redirection si l'utilisateur tente de soumettre sans être connecté
if (!$isConnected && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: connexion/login.php");
    exit();
}

// --- TRAITEMENT DES FORMULAIRES ---
if ($isConnected && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        // 1. MISE À JOUR DU PROFIL
        if (isset($_POST['update_profile'])) {
            $newName  = trim($_POST['name'] ?? '');
            $newEmail = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $newPass  = $_POST['password'] ?? '';

            if (!empty($newName) && $newEmail) {
                if (!empty($newPass)) {
                    $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
                    $sql = "UPDATE `users` SET `name` = ?, `email` = ?, `password` = ? WHERE `id_users` = ?";
                    $obj->query2($sql, [$newName, $newEmail, $hashedPass, $userId]);
                } else {
                    $sql = "UPDATE `users` SET `name` = ?, `email` = ? WHERE `id_users` = ?";
                    $obj->query2($sql, [$newName, $newEmail, $userId]);
                }

                $_SESSION = array();
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
                session_destroy();

                header("Location: connexion/login.php?updated=1");
                exit();
            }
        }

        // 2. MISE À JOUR DE LA NEWSLETTER
        if (isset($_POST['update_newsletter'])) {
            $newsletter = isset($_POST['newsletter']) ? 1 : 0;

            $sql = "UPDATE `users` SET `newsletter` = ? WHERE `id_users` = ?";
            $obj->query2($sql, [$newsletter, $userId]);

            $_SESSION['newsletter'] = $newsletter;

            header("Location: " . $_SERVER['PHP_SELF'] . "?saved=1");
            exit();
        }

    } catch (Throwable $e) {
        // En cas d'erreur, affiche le détail au lieu d'un écran noir
        die("Erreur survenue lors du traitement : " . $e->getMessage());
    }
}

// --- RÉCUPÉRATION DES DONNÉES EN BDD POUR AFFICHAGE ---
if ($isConnected) {
    $userData = $obj->query2("SELECT `name`, `email`, `newsletter` FROM `users` WHERE `id_users` = ?", [$userId]);
    if (!empty($userData)) {
        $userName       = $userData[0]['name'];
        $userEmail      = $userData[0]['email'];
        $userNewsletter = (int)$userData[0]['newsletter'];

        // Synchronisation des variables de session
        $_SESSION['name']       = $userName;
        $_SESSION['email']      = $userEmail;
        $_SESSION['newsletter'] = $userNewsletter;
    }
} else {
    $userName       = '';
    $userEmail      = '';
    $userNewsletter = 0;
}

// Informations de version du site
$version      = require __DIR__ . '/version.php';
$siteVersion  = $version['siteVersion'];
$appVersion   = $siteVersion . '.' . $version['appBuild'];
$updated      = $version['updated'];
$dateAffichee = "{$updated['day']} {$updated['num']} {$updated['month']} {$updated['year']}";

// Menu dynamique
$menuTable = $isConnected ? 'menu_connect' : 'menu_principal';
$menuItems = $obj->query("SELECT * FROM `$menuTable` ORDER BY `parent` ASC, `id` ASC");

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
    }

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
    
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-5">
      <a href="#" class="btn btn-outline-light rounded-pill px-4 shadow-sm">
        <i class="fa-solid fa-arrow-left me-2"></i> Retour
      </a>
      <h2 class="text-white mb-0 fw-600">Paramètres du Compte</h2>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="glass-container text-start shadow-lg p-4 p-md-5">
          
          <!-- FORMULAIRE 1 : INFORMATIONS DE COMPTE (AVEC DÉCONNEXION) -->
          <form id="profileForm" action="" method="POST">
            <h5 class="text-white mb-4"><i class="fa-solid fa-user-gear me-2 text-info"></i>Profil & Sécurité</h5>

            <div class="mb-4">
              <label for="userNameInput" class="text-white-50 small fw-bold mb-2">NOM D'UTILISATEUR</label>
              <input type="text" id="userNameInput" name="name" class="input-glass form-control text-white" value="<?= htmlspecialchars($userName ?? '') ?>" required>
            </div>

            <div class="mb-4">
              <label for="userEmailInput" class="text-white-50 small fw-bold mb-2">ADRESSE EMAIL</label>
              <input type="email" id="userEmailInput" name="email" class="input-glass form-control text-white" value="<?= htmlspecialchars($userEmail ?? '') ?>" required>
            </div>

            <div class="mb-4">
              <label for="userPasswordInput" class="text-white-50 small fw-bold mb-2">NOUVEAU MOT DE PASSE</label>
              <input type="password" id="userPasswordInput" name="password" class="input-glass form-control text-white" placeholder="••••••••" autocomplete="new-password">
              <small class="text-white-50 mt-1 d-block">Laissez vide pour conserver le mot de passe actuel.</small>
            </div>

            <!-- Bouton déclenchant la modal de déconnexion -->
            <button type="button" class="btn btn-cosmic-glass w-100 py-3 mb-4 fw-bold text-white" data-bs-toggle="modal" data-bs-target="#confirmModal">
              <i class="fa-solid fa-rotate me-2"></i> Mettre à jour le profil
            </button>

            <!-- Modal de confirmation -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-container shadow-lg border-0">
                  <div class="modal-body text-center p-4 p-md-5">
                    <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 3rem;"></i>
                    <h4 class="text-white mb-3" id="confirmModalLabel">Attention !</h4>
                    <p class="text-white-50 mb-4">
                      La modification de vos identifiants entraînera une <b>déconnexion immédiate</b> pour des raisons de sécurité.
                    </p>
                    <div class="d-grid gap-2">
                      <button type="submit" name="update_profile" class="btn btn-primary rounded-pill py-2 fw-bold">
                        Confirmer et se déconnecter
                      </button>
                      <button type="button" class="btn btn-link text-white-50" data-bs-dismiss="modal">
                        Annuler
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <?php if ($_SESSION['email'] == 'adrienb39@yahoo.com'): ?>
            <hr class="border-secondary my-5">

          <?php
session_start();

// Génération d'un token dynamique unique s'il n'existe pas encore
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = $_SESSION['csrf_token'];
?>

<a href="/api/scripts.php?token=<?php echo urlencode($token); ?>" class="btn-modern">
  <span class="btn-content">
    <i class="fa-solid fa-sliders"></i>
    <span>Centre de contrôle</span>
  </span>
  <i class="fa-solid fa-arrow-right btn-arrow"></i>
</a>
<style>
  /* ==========================================================================
   Bouton Modern (Grand format + Alignement Flexbox parfait)
   ========================================================================== */

.btn-modern {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  
  /* Dimensions agrandies */
  padding: 1.1rem 2.2rem;
  border-radius: 18px;
  font-size: 1.15rem;
  font-weight: 700;
  line-height: 1.2;
  
  /* Apparence et couleurs */
  color: #ffffff;
  text-decoration: none;
  background: linear-gradient(135deg, rgba(13, 202, 240, 0.15) 0%, rgba(13, 110, 253, 0.25) 100%);
  border: 1px solid rgba(13, 202, 240, 0.4);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25), 0 0 15px rgba(13, 202, 240, 0.15);
  
  /* Effet verre dépoli */
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  
  /* Transitions et rendu */
  overflow: hidden;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
              background 0.3s ease,
              border-color 0.3s ease,
              box-shadow 0.3s ease;
}

/* Effet de brillance au survol */
.btn-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
  transition: left 0.6s ease;
  pointer-events: none;
}

/* Alignement du conteneur texte + icône de gauche */
.btn-content {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

/* Correction spécifique des icônes FontAwesome (alignement vertical) */
.btn-icon,
.btn-arrow {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.btn-icon {
  color: #0dcaf0;
  font-size: 1.35rem;
  transition: transform 0.3s ease;
}

.btn-arrow {
  font-size: 1.1rem;
  opacity: 0.7;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

/* ==========================================================================
   États d'interaction (Hover, Active, Focus)
   ========================================================================== */

.btn-modern:hover {
  color: #ffffff;
  border-color: rgba(13, 202, 240, 0.8);
  background: linear-gradient(135deg, rgba(13, 202, 240, 0.3) 0%, rgba(13, 110, 253, 0.4) 100%);
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(13, 202, 240, 0.4);
}

.btn-modern:hover::before {
  left: 100%;
}

.btn-modern:hover .btn-icon {
  transform: scale(1.2) rotate(-10deg);
}

.btn-modern:hover .btn-arrow {
  opacity: 1;
  transform: translateX(6px);
}

.btn-modern:active {
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(13, 202, 240, 0.25);
}
</style>
<?php endif; ?>

          <hr class="border-secondary my-5">

          <!-- FORMULAIRE 2 : PREFERENCES NEWSLETTER (SANS DÉCONNEXION) -->
          <form id="newsletterForm" action="" method="POST">
            <h5 class="text-white mb-4"><i class="fa-solid fa-envelope-open-text me-2 text-info"></i>Abonnements</h5>

            <div class="mb-4">
              <div class="form-check form-switch mb-2" style="display: flex; justify-content: space-between; align-items: center;">
                <input class="form-check-input" type="checkbox" role="switch" id="newsletterCheck" name="newsletter" value="1" <?= !empty($userNewsletter) ? 'checked' : '' ?>>
                <label class="form-check-label text-white fw-bold" for="newsletterCheck">
                  Newsletter & Mises à jour Meteastro
                </label>
              </div>
              <small class="text-white-50 d-block ms-4">
                Recevez les actualités astronomiques et les annonces de fonctionnalités (version 2.5.0+).
              </small>
            </div>

            <button type="submit" name="update_newsletter" class="btn btn-outline-info w-100 py-2 fw-bold mb-4">
              <i class="fa-solid fa-floppy-disk me-2"></i> Enregistrer les préférences
            </button>
          </form>

          <hr class="border-secondary my-5">

          <!-- WIDGET DE VERSION (INFORMATIF) -->
          <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary d-flex justify-content-between align-items-center">
            <div>
              <span class="text-white-50 small d-block">VERSION INSTALLÉE</span>
              <span id="currentVersionDisplay" class="text-white fw-bold">v<?= htmlspecialchars($siteVersion ?? '1.0.0') ?></span>
            </div>
            <div id="versionStatusBadge">
              <span class="badge bg-secondary">Vérification en cours...</span>
            </div>
          </div>

          <input type="hidden" id="clientVersionInput" value="<?= htmlspecialchars($siteVersion ?? '1.0.0') ?>">

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

      <style>
        /* ==========================================================================
   1. VARIABLES DE THÈME
   ========================================================================== */
        :root {
          --pwa-bg-header: rgba(243, 243, 244, 0.90);
          --pwa-border-color: rgba(0, 0, 0, 0.07);
          --pwa-text-main: #1f1f1f;
          --pwa-text-inactive: #5f6368;
          --pwa-text-active: #0b57d0;
          --pwa-accent-2: #7c3aed;
          --pwa-shadow-nav: 0 8px 30px rgba(0, 0, 0, 0.12);
          --pwa-card-bg: #ffffff;
          --pwa-overlay-bg: rgba(15, 15, 20, 0.55);
        }

        @media (prefers-color-scheme: dark) {
          :root {
            --pwa-bg-header: rgba(31, 31, 31, 0.90);
            --pwa-border-color: rgba(255, 255, 255, 0.08);
            --pwa-text-main: #e8e8e8;
            --pwa-text-inactive: #9aa0a6;
            --pwa-text-active: #8ab4f8;
            --pwa-accent-2: #c084fc;
            --pwa-shadow-nav: 0 8px 32px rgba(0, 0, 0, 0.55);
            --pwa-card-bg: #1a1a1e;
            --pwa-overlay-bg: rgba(0, 0, 0, 0.72);
          }
        }

        /* ==========================================================================
   2. BOUTON FLOTTANT (FAB) — verre dépoli + halo pulsé
   ========================================================================== */
        .pwa-fab-trigger {
          position: relative;
          background: color-mix(in srgb, var(--pwa-card-bg) 65%, transparent);
          backdrop-filter: blur(14px) saturate(160%);
          -webkit-backdrop-filter: blur(14px) saturate(160%);
          color: var(--pwa-text-active);
          border: 1px solid var(--pwa-border-color);
          width: 42px;
          height: 42px;
          border-radius: 50%;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: visible;
          transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
            background-color 0.25s ease,
            box-shadow 0.25s ease;
        }

        .pwa-fab-trigger:hover {
          background: color-mix(in srgb, var(--pwa-card-bg) 82%, transparent);
          box-shadow: 0 4px 16px rgba(11, 87, 208, 0.18);
          transform: translateY(-2px);
        }

        .pwa-fab-trigger:active {
          transform: scale(0.90);
        }

        .pwa-fab-trigger svg {
          width: 20px;
          height: 20px;
          fill: currentColor;
          position: relative;
          z-index: 2;
        }

        /* Halo pulsé discret, en fond du bouton */
        .pwa-fab-pulse {
          position: absolute;
          inset: 0;
          border-radius: 50%;
          background: radial-gradient(circle, var(--pwa-text-active) 0%, transparent 70%);
          opacity: 0.35;
          animation: pwa-pulse 2.6s ease-in-out infinite;
          z-index: 1;
        }

        @keyframes pwa-pulse {

          0%,
          100% {
            transform: scale(0.85);
            opacity: 0.25;
          }

          50% {
            transform: scale(1.15);
            opacity: 0.5;
          }
        }

        /* ==========================================================================
   3. OVERLAY & MODAL
   ========================================================================== */
        .pwa-modal-overlay {
          position: fixed;
          inset: 0;
          background: var(--pwa-overlay-bg);
          backdrop-filter: blur(6px);
          -webkit-backdrop-filter: blur(6px);
          z-index: 1000;
          display: flex;
          align-items: center;
          justify-content: center;
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.35s ease;
          padding: 16px;
          box-sizing: border-box;
        }

        .pwa-modal-overlay.is-open {
          opacity: 1;
          pointer-events: auto;
        }

        .pwa-modal-container {
          width: 100%;
          max-width: 380px;
          transform: translateY(24px) scale(0.96);
          opacity: 0;
          transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        }

        .pwa-modal-overlay.is-open .pwa-modal-container {
          transform: translateY(0) scale(1);
          opacity: 1;
        }

        .pwa-card {
          background: var(--pwa-card-bg);
          border: 1px solid var(--pwa-border-color);
          border-radius: 32px;
          padding: 32px 28px 28px;
          text-align: center;
          box-shadow: var(--pwa-shadow-nav);
          position: relative;
          overflow: hidden;
        }

        /* Mesh de couleur discret en arrière-plan du card */
        .pwa-card::before {
          content: "";
          position: absolute;
          top: -60%;
          left: -20%;
          width: 160%;
          height: 160%;
          background:
            radial-gradient(circle at 20% 20%, color-mix(in srgb, var(--pwa-text-active) 18%, transparent) 0%, transparent 45%),
            radial-gradient(circle at 80% 10%, color-mix(in srgb, var(--pwa-accent-2) 14%, transparent) 0%, transparent 45%);
          pointer-events: none;
          z-index: 0;
        }

        .pwa-card>* {
          position: relative;
          z-index: 1;
        }

        .pwa-btn-close {
          position: absolute;
          top: 18px;
          right: 18px;
          background: color-mix(in srgb, var(--pwa-text-main) 6%, transparent);
          border: none;
          color: var(--pwa-text-inactive);
          width: 30px;
          height: 30px;
          border-radius: 50%;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: background 0.2s, color 0.2s, transform 0.2s;
        }

        .pwa-btn-close:hover {
          background: color-mix(in srgb, var(--pwa-text-main) 12%, transparent);
          color: var(--pwa-text-main);
          transform: rotate(90deg);
        }

        /* ==========================================================================
   4. LOGO
   ========================================================================== */
        .pwa-logo-wrap {
          position: relative;
          width: 72px;
          height: 72px;
          margin: 0 auto 18px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .pwa-logo-glow {
          position: absolute;
          inset: -10px;
          border-radius: 24px;
          background: conic-gradient(from 0deg, var(--pwa-text-active), var(--pwa-accent-2), var(--pwa-text-active));
          filter: blur(16px);
          opacity: 0.45;
          animation: pwa-spin 6s linear infinite;
        }

        @keyframes pwa-spin {
          to {
            transform: rotate(360deg);
          }
        }

        .pwa-logo {
          position: relative;
          width: 72px;
          height: 72px;
          border-radius: 20px;
          overflow: hidden;
          box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
        }

        .pwa-logo img {
          width: 100%;
          height: 100%;
          object-fit: contain;
          display: block;
        }

        /* ==========================================================================
   5. TEXTES
   ========================================================================== */
        .pwa-rainbow-title {
          margin: 0 0 8px 0;
          font-size: 21px;
          font-weight: 800;
          letter-spacing: -0.02em;
          background: linear-gradient(100deg,
              hsl(210, 90%, 55%), hsl(260, 85%, 60%), hsl(320, 80%, 58%),
              hsl(20, 90%, 58%), hsl(210, 90%, 55%));
          background-size: 300% 100%;
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
          animation: pwa-rainbow-fluid 12s ease-in-out infinite;
        }

        @keyframes pwa-rainbow-fluid {

          0%,
          100% {
            background-position: 0% 50%;
          }

          50% {
            background-position: 100% 50%;
          }
        }

        .pwa-subtitle {
          color: var(--pwa-text-inactive);
          margin: 0 0 18px 0;
          font-size: 14px;
          line-height: 1.5;
        }

        .pwa-badge-status {
          font-size: 12.5px;
          font-weight: 600;
          color: var(--pwa-text-inactive);
          background: color-mix(in srgb, var(--pwa-text-main) 5%, transparent);
          border: 1px solid var(--pwa-border-color);
          padding: 7px 14px;
          border-radius: 100px;
          display: inline-flex;
          align-items: center;
          gap: 7px;
          margin-bottom: 20px;
        }

        .pwa-badge-dot {
          width: 7px;
          height: 7px;
          border-radius: 50%;
          background: var(--pwa-text-active);
          animation: pwa-blink 1.6s ease-in-out infinite;
        }

        @keyframes pwa-blink {

          0%,
          100% {
            opacity: 1;
          }

          50% {
            opacity: 0.3;
          }
        }

        /* ==========================================================================
   6. INSTRUCTIONS
   ========================================================================== */
        .pwa-list-instructions {
          text-align: left;
          list-style: none;
          padding: 0;
          margin: 0 0 20px 0;
          display: flex;
          flex-direction: column;
          gap: 12px;
        }

        .pwa-list-instructions li {
          display: flex;
          align-items: flex-start;
          gap: 12px;
          font-size: 14px;
          line-height: 1.5;
          color: var(--pwa-text-main);
        }

        .pwa-step-num {
          flex-shrink: 0;
          width: 22px;
          height: 22px;
          border-radius: 50%;
          background: color-mix(in srgb, var(--pwa-text-active) 14%, transparent);
          color: var(--pwa-text-active);
          font-size: 12px;
          font-weight: 700;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .pwa-inline-icon {
          font-size: 15px;
          opacity: 0.8;
        }

        /* ==========================================================================
   7. BOUTON D'INSTALLATION
   ========================================================================== */
        .pwa-btn-install {
          display: none;
          width: 100%;
          padding: 15px;
          font-size: 15px;
          border-radius: 16px;
          background: linear-gradient(135deg, var(--pwa-text-active), var(--pwa-accent-2));
          color: #fff;
          font-weight: 700;
          border: none;
          cursor: pointer;
          align-items: center;
          justify-content: center;
          gap: 8px;
          box-shadow: 0 6px 18px color-mix(in srgb, var(--pwa-text-active) 35%, transparent);
          transition: transform 0.15s ease, box-shadow 0.25s ease;
        }

        .pwa-btn-install:hover {
          box-shadow: 0 8px 22px color-mix(in srgb, var(--pwa-text-active) 45%, transparent);
        }

        .pwa-btn-install:active {
          transform: scale(0.97);
        }

        /* ==========================================================================
   8. AFFICHAGE MODE APPLI INSTALLÉE
   ========================================================================== */
        .pwa-dashboard-view {
          display: none !important;
        }

        @media (display-mode: standalone),
        (display-mode: fullscreen),
        (display-mode: minimal-ui) {
          .pwa-dashboard-view {
            display: block !important;
          }

          .pwa-browser-view,
          .pwa-fab-trigger {
            display: none !important;
          }
        }
      </style>

      <button class="pwa-fab-trigger pwa-browser-view" id="pwa-action-open" title="Installer l'application">
        <svg viewBox="0 0 24 24">
          <path d="M5 20h14v-2H5v2zm7-2l5-5h-4V4h-2v7H7l5 5z" />
        </svg>
      </button>
    </div>
  </nav>

  <div class="pwa-modal-overlay pwa-browser-view" id="pwa-component-modal">
    <main class="pwa-modal-container">
      <div class="pwa-card">
        <button class="pwa-btn-close" id="pwa-action-close" aria-label="Fermer">
          <svg viewBox="0 0 24 24" width="16" height="16">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"
              fill="none" />
          </svg>
        </button>

        <div class="pwa-logo-wrap">
          <div class="pwa-logo"><img src="/ressources/logo.png" alt="Logo"></div>
        </div>

        <h2 class="pwa-rainbow-title">Installer Meteastro</h2>

        <p class="pwa-subtitle">
          Ajoutez l'application à votre écran d'accueil pour profiter d'une expérience plein écran fluide.
        </p>

        <div class="pwa-badge-status" id="pwa-text-status">
          <span class="pwa-badge-dot"></span>
          Analyse du système...
        </div>

        <div id="pwa-guide-ios" class="pwa-guide" style="display: none;">
          <ol class="pwa-list-instructions">
            <li><span class="pwa-step-num">1</span>Ouvrez le menu de partage de Safari <span
                class="pwa-inline-icon">⎋</span></li>
            <li><span class="pwa-step-num">2</span>Faites défiler et sélectionnez <strong>Sur l'écran d'accueil</strong>
            </li>
            <li><span class="pwa-step-num">3</span>Validez en cliquant sur <strong>Ajouter</strong></li>
          </ol>
        </div>

        <div id="pwa-guide-generic" class="pwa-guide" style="display: none;">
          <ol class="pwa-list-instructions" id="pwa-text-instructions">
            <li><span class="pwa-step-num">1</span>Ouvrez les options de votre navigateur <span
                class="pwa-inline-icon">⋮</span></li>
            <li><span class="pwa-step-num">2</span>Choisissez <strong>Installer l'application</strong> ou <i>Ajouter à
                l'écran d'accueil</i></li>
          </ol>
          <button id="pwa-action-install" class="pwa-btn-install">
            <span>Installer maintenant</span>
            <svg viewBox="0 0 24 24" width="18" height="18">
              <path d="M5 20h14v-2H5v2zm7-2l5-5h-4V4h-2v7H7l5 5z" />
            </svg>
          </button>
        </div>
      </div>
    </main>
  </div>

  <main class="container my-5">
  </main>

  <div class="container-mobile">
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

    <a href="/#contacts" data-anchor="contacts"
      class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && (strpos($_SERVER['REQUEST_URI'], '#contacts') !== false || isset($_GET['contact']))) ? 'active' : ''; ?>">
      <span class="nav-icon">✉️</span>
      <span class="nav-label">Contact</span>
    </a>

    <?php if (isset($isConnected) && $isConnected === true): ?>
      <a href="/redirect.php"
        class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'redirect.php') ? 'active' : ''; ?>">
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
          <a href="#parametres/"
            class="submenu-item <?php echo (basename($_SERVER['PHP_SELF']) == '#parametres/') ? 'active' : ''; ?>">
            <span class="submenu-icon">⚙️</span> Paramètres
          </a>
          <hr class="submenu-divider">
          <a href="/connexion/logout.php" class="submenu-item logout-item">
            <span class="submenu-icon">🚪</span> Déconnexion
          </a>
        </div>
      </div>
    <?php else: ?>
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
  <style>
    /* ==========================================================================
   1. VARIABLES DE THÈME (STYLE FLOTTANT ANDROID MATERIAL YOU)
   ========================================================================== */
    :root {
      /* --- Mode Clair (Couleurs de surface Android) --- */
      --bg-header: rgba(243, 243, 244, 0.90);
      /* Opacité 90% teintée Android 14 */
      --bg-nav: rgba(243, 243, 244, 0.88);
      /* Transparence équilibrée native */
      --border-color: rgba(0, 0, 0, 0.06);
      /* Délimitation subtile */
      --text-main: #1f1f1f;
      --text-inactive: #444746;
      /* Teinte "On-Surface Variant" Android */
      --text-active: #0b57d0;
      /* Bleu primaire standard Material 3 */
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
        --text-active: #a8c7fa;
        /* Bleu clair adaptatif écran sombre */
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
      display: none !important;
      /* Masqué sur navigateur de bureau */
    }

    .container-mobile {
      padding-bottom: 30px;
      /* Espace standard de fin de page */
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
    @media (display-mode: standalone),
    (display-mode: fullscreen),
    (display-mode: minimal-ui) {
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
        border-radius: 24px;
        /* Format "pilule flottante" Material You */

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
        font-weight: 500;
        /* Medium font-weight natif Android */
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
        transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1);
        /* Courbe haptique Android */
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
        transform: scale(0.9);
        /* Feedback haptique visuel doux Android */
      }

      .nav-item.active {
        color: var(--text-active);
        font-weight: 700;
        /* Met en valeur l'élément actif */
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
        bottom: 72px;
        /* Placé idéalement juste au-dessus de la bottom-nav */

        /* CORRECTION ANGLE : Aligné sur la droite de l'onglet avec un retrait de sécurité */
        right: 0;
        margin-right: 8px;
        width: 180px;

        /* Design & Couleurs adaptatives */
        background: var(--bg-nav);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-nav);
        border-radius: 20px;
        /* Coins généreusement arrondis style Material 3 */
        padding: 6px;
        z-index: 1001;
        box-sizing: border-box;

        /* GESTION DE L'ANIMATION (Sans display: none pour préserver la fluidité) */
        visibility: hidden;
        opacity: 0;
        transform: translateY(12px) scale(0.96);
        /* Effet d'apparition du bas vers le haut */
        transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1),
          opacity 0.22s ease,
          visibility 0.22s ease;
      }

      /* ÉTAT ACTIF : Classe injectée par le JavaScript à l'ouverture */
      .nav-submenu.open {
        visibility: visible;
        opacity: 1;
        transform: translateY(0) scale(1);
        /* Rendu stable et centré sur sa zone */
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
        .submenu-item {
          color: #e3e3e3;
        }
      }

      .submenu-item:hover,
      .submenu-item.active {
        background: rgba(0, 0, 0, 0.05);
      }

      @media (prefers-color-scheme: dark) {

        .submenu-item:hover,
        .submenu-item.active {
          background: rgba(255, 255, 255, 0.08);
        }
      }

      .submenu-icon {
        margin-right: 10px;
        font-size: 16px;
      }

      .submenu-divider {
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        margin: 6px 0;
      }

      @media (prefers-color-scheme: dark) {
        .submenu-divider {
          border-top-color: rgba(255, 255, 255, 0.1);
        }
      }

      .logout-item {
        color: #dc3545;
      }

      /* Rouge pour la déconnexion */

      /* Conteneur pour aligner le texte et la flèche sur la même ligne */
      .nav-label-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        /* Espace entre le texte et la flèche */
      }

      /* Style de la petite flèche */
      .submenu-caret {
        font-size: 8px;
        /* Plus discret que le texte */
        color: #757575;
        /* Couleur grise par défaut */
        transition: transform 0.2s ease;
        /* Animation fluide de rotation */
        display: inline-block;
      }

      @media (prefers-color-scheme: dark) {
        .submenu-caret {
          color: #aaa;
        }
      }

      /* Changement de couleur au survol/actif de l'onglet parent */
      .nav-item-has-submenu:hover .submenu-caret {
        color: #0b57d0;
        /* S'adapte à la couleur active de votre thème */
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
        0% {
          transform: scale(0.95);
          box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        }

        70% {
          transform: scale(1);
          box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
        }

        100% {
          transform: scale(0.95);
          box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
        }
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
        margin-right: 4px;
        /* Espace léger avant le logo */
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

      .sun-icon {
        color: #ffb703;
      }

      .moon-icon {
        color: #a2d2ff;
      }

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
        bottom: 20px;
        /* Distance par rapport au bas de l'écran une fois "bloqué" */

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
        background: #0d1117;
        /* Mettre la couleur de fond sombre par défaut de votre app */
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 16px 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        margin: 0 auto;
      }

      .lightmode .pwa-about-dialog .pwa-app-dashboard {
        background: #ffffff;
        /* Mettre la couleur de fond claire par défaut de votre app */
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
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
  const currentVersionInput = document.getElementById('clientVersionInput');
  const badgeContainer = document.getElementById('versionStatusBadge');

  if (!badgeContainer) return;

  const currentVersion = currentVersionInput?.value || '0.0.0';

  try {
    const response = await fetch('/api/get-latest-version.php');

    if (!response.ok) {
      throw new Error(`Erreur serveur HTTP : ${response.status}`);
    }

    const data = await response.json();
    const { version: latestVersion = '1.0.0' } = data || {};

    if (isVersionOutdated(currentVersion, latestVersion)) {
      badgeContainer.innerHTML = `
        <span class="badge bg-warning text-dark">
          <i class="fa-solid fa-triangle-exclamation me-1"></i> Mise à jour v${latestVersion} disponible
        </span>`;
    } else {
      badgeContainer.innerHTML = `
        <span class="badge bg-success">
          <i class="fa-solid fa-check me-1"></i> À jour
        </span>`;
    }
  } catch (error) {
    console.error('Erreur lors de la vérification de version :', error);
    badgeContainer.innerHTML = `<span class="badge bg-secondary">Inconnu</span>`;
  }
});

// Fonction utilitaire de comparaison de versions semver
function isVersionOutdated(current, latest) {
  const c = current.split('.').map(Number);
  const l = latest.split('.').map(Number);

  for (let i = 0; i < 3; i++) {
    const currentNum = c[i] || 0;
    const latestNum = l[i] || 0;

    if (latestNum > currentNum) return true;
    if (latestNum < currentNum) return false;
  }
  return false;
}
  </script>
</body>

</html>