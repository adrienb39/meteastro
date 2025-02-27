<?php
session_start();
require_once "config/connexion_bdd.php";

$dbType = 'mysqli';

if ($dbType === 'pdo') {
    $db = createPdoConnection();
} else {
    $mysqli = createMysqliConnection();
}
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
    <link rel="icon" type="image/png" sizes="16x16" href="/ressources/logo.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="../CSS/style.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/js/all.min.js"></script>
    <script src="/import_dans_le_php/menu.js" defer></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css'>
    <style>
        .note-editor.note-airframe,
        .note-editor.note-frame {
            border: 2px solid gray;
        }

        .note-frame {
            -ms-box-sizing: border-box;
            box-sizing: border-box;
            color: #000;
            font-family: sans-serif;
            border-radius: 10px;
            margin: 10px 10%;
            width: 100%;
            border: 2px solid gray;
        }

        .note-toolbar {
            padding: 10px 5px;
            color: #333;
            background-color: #f5f5f5;
            border-bottom: 1px solid;
            border-color: #ddd;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
    </style>
</head>

<body>
<?php if (isset($_SESSION['email']) && isset($_SESSION['password'])) { ?>
    <div class="container">
        <div class="row">
            <div class="col col-5 mx-auto mt-4">
                <div class="text-center mt-5">
                    <h1 class="fs-1">
                        Cette page vous permet d'ajouter du contenu !
                    </h1>
                    <p class="fs-4 text-danger opacity-75">Faites décoler vos idées...</p>
                    <?php include "divers/contenu.php"; ?>
                </div>
                <a href="index.php" class="btn btn-close-white btn-outline-danger fs-5 mb-5"
                    style="text-decoration: none;">Retour sur la page d'accueil du Site Web de
                    votre
                    espace</a>
            </div>
            <div class="col col-7 d-md-block d-none">
                <img src="/ressources/contenu.png" class="w-100">
            </div>
        </div>
    </div>
    <!-- <p>Cette page permet d'ajouter du contenu !</p>
    <p>Pour pouvoir ajouter du contenu cliquez sur ce lien :
    </p> -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('#contenu-astronomie').summernote({
                height: 300,
                placeholder: '<b>Entrez du contenu pour l\'Astronomie</b><br>Vous pouvez personnaliser votre texte'
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#contenu-meteorologie').summernote({
                height: 300,
                placeholder: '<b>Entrez du contenu pour la Météorologie</b><br>Vous pouvez personnaliser votre texte'
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <!-- partial -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <?php } else {
        header('Location: connexion/login.php');
    } ?>
</body>

</html>