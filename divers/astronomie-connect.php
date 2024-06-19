<?php
// Initialiser la session
session_start();
// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if (!isset ($_SESSION["type"])) {
    header("Location: /connexion/login.php");
    exit();
}
?>
<?php
require_once '../config/connexion_bdd.php'
    ?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="icon" type="image/png" sizes="16x16" href="/ressources/logo.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="/divers/divers.css">
    <!-- <style>
        .blc-seemore{
            position: relative;
            margin-left: 5px;
        }

        span.hide { display: none; }

label.seemore::before { content: "\0026EE"; }
label.seemore:hover { cursor: pointer; }

input[type='checkbox'].seemore {
  position: absolute;
  left: -9999px;
  top: -9999px;
}

input.seemore:checked~span.hide { display: inline; background-color: black; color: white; position: relative; z-index: 99; padding: 3px; }
    </style> -->
</head>

<body>
    <?php
    include "../import_dans_le_php/menu-connect.php";
    ?>
    <!-- Contenu de la page principale pour l'Astronomie -->
    <div class="navbar-onglets">
        <label class="label-onglets" for="toggle">☰</label>
        <input type="checkbox" id="toggle">
        <div class="container-onglets">
            <?php
            $sql = "SELECT * FROM astronomie";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Afficher les résultats de chaque ligne
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='onglets' data-anim=" . $row["id"] . ">" . $row["title"] . "</div>";
                    echo "<div class='container'>
                        <div class='contenu' data-anim=" . $row["id"] . ">
                            <h3>" . $row["title_contenu"] . "</h3>
                            <hr>
                            <p>" . $row["contenu"] . "</p>
                        </div>
                        </div>";
                }
            } else {
                echo "0 resultat";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <?php
    include "../cookie/cookie.php";
    ?>
    <?php
    include "../import_dans_le_php/footer.php";
    ?>

    <script src="app.js"></script>
</body>

</html>