<?php
session_start();
require_once 'db.class.php';

// Authentification simplifiée
$isConnected = isset($_SESSION['email']) && isset($_SESSION['password']);
$userName = $isConnected ? $_SESSION['name'] : '';

// Initialisation DB
$obj = new Db();
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
      --accent-color: #3b82f6;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: #05070a;
      color: #e2e8f0;
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
  </style>
</head>

<body>

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Protection globale des images
    document.addEventListener('contextmenu', e => {
      if (e.target.tagName === 'IMG') e.preventDefault();
    });
    document.addEventListener('dragstart', e => {
      if (e.target.tagName === 'IMG') e.preventDefault();
    });
  </script>
</body>

</html>