<?php
session_start();
// Supposons que vous ayez une session qui indique si l'utilisateur est connecté
$isConnected = isset($_SESSION['email']) && isset($_SESSION['password']); // Adaptez cela à votre logique d'authentification
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Ce site permet d'avoir les informations d'astronomie et de météorologie et de contacter pour avoir des renseignement supplémentaire et bien d'autre">
  <meta name="keywords" content="">
  <title>Meteastro : Astronomie / météorologie</title>
  <link rel="icon" type="image/png" sizes="16x16" href="/ressources/logo.png">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="theme-color" content="#ffffff">
  <link rel="stylesheet" href="../CSS/style.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/js/all.min.js"></script>
  <script src="/import_dans_le_php/menu.js" defer></script>
</head>

<body>
  <!-- logo -->
  <div class="div-logo">
    <a class="logo" href="/">
      <img class="logo-icon" src="/ressources/logo.png" alt="logo">
      <span class="title-logo">METEASTRO</span>
    </a>
    <div class="marquee">
      <div class="info">Bienvenue sur le site de Meteastro !</div>
    </div>
  </div>
  
  <!-- menu -->
  <div class="container" id="nav">
    <nav>
      <div class="mobile-nav">
        <span>Menu</span>
        <div class="nav-btn">
          <i class="fas fa-bars"></i>
        </div>
      </div>

      <ul class="nav">
        <?php
        include 'db.class.php';
        $obj = new Db;

        // Choisissez la requête selon l'état de connexion
        $result = $isConnected ? $obj->query('SELECT * from menu_connect') : $obj->query('SELECT * from menu_principal');

        menu($result);

        function menu($data, $parent_id = 0) {
          foreach ($data as $key => $value) {
            if ($value['parent'] == $parent_id) {
              html($data, $value);
            }
          }
        }

        function html($data, $menu) {
          $count = 0;
          foreach ($data as $key => $value) {
            if ($value['parent'] == $menu['id']) {
              $count++;
            }
          }

          if ($count > 0) {
            echo '<li class=dropdown><a href=' . $menu['url'] . '>' . ucfirst($menu['menu_name']) . '</a><ul>';
            menu($data, $menu['id']);
            echo '</ul></li>';
          } else {
            echo '<li><a href=' . $menu['url'] . '>' . ucfirst($menu['menu_name']) . '</a></li>';
          }
        }
        ?>
      </ul>
    </nav>
  </div>
  
  <script>
    window.onscroll = function () { myFunction() };

    var navbar = document.getElementById("nav");
    var sticky = navbar.offsetTop;

    function myFunction() {
      if (window.pageYOffset >= sticky) {
        navbar.classList.add("sticky");
        navbar.classList.remove("relative");
      } else {
        navbar.classList.remove("sticky");
        navbar.classList.add("relative");
      }
    }
  </script>
</body>
</html>
