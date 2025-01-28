<?php
require_once '../../config/connexion_bdd.php';

$dbType = 'pdo';

if ($dbType === 'pdo') {
    $db = createPdoConnection();
} else {
    $mysqli = createMysqliConnection();
}

function getArticle($db, $id) {
    $sql = 'SELECT * FROM astronomie, usertable WHERE astronomie.id_users = usertable.id_users AND astronomie.id = :id';
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Lier le paramètre

    if (!$stmt->execute()) {
        die('Erreur lors de l\'exécution de la requête.');
    }

    if ($stmt->rowCount() === 1) {
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'article trouvé
    } else {
        die('Cet article n\'existe pas !');
    }
}

// Vérification de l'ID dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = htmlspecialchars($_GET['id']);
    $article = getArticle($db, $id);
    
    // Assignation des variables à partir de l'article récupéré
    $title = $article['title'];
    $filename = $article['filename'];
    $title_contenu = $article['title_contenu'];
    $contenu = $article['contenu'];
    $date_astronomie = $article['date_astronomie'];
    $name = $article['name'];
} else {
    die('Erreur : ID manquant.');
}
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
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        .card {
            overflow: hidden;
            box-shadow: 0px 2px 20px var(--clr-gray-light);
            background: white;
            border-radius: 0.5rem;
            position: relative;
            width: 100%;
            margin: 1rem;
            transition: 250ms all ease-in-out;
            cursor: pointer;
            color: black;
        }

        .darkmode .card {
            overflow: hidden;
            box-shadow: white 0 0 10px;
            background: var(--night-300);
            border-radius: 0.5rem;
            position: relative;
            width: 100%;
            margin: 1rem;
            transition: 250ms all ease-in-out;
            cursor: pointer;
            color: white;
        }

        .card-body {
            margin: 15rem 1rem 1rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card:hover {
            transform: none;
            cursor: auto;
        }

        .card-body-contenu {
            margin: 5rem 1rem 1rem 1rem;
        }

        .banner-img {
            position: absolute;
            object-fit: contain;
            height: 14rem;
            width: 100%;
        }

        .card-profile {
            margin-top: initial;
        }
    </style>
</head>

<body>
    <?php
    include "../../import_dans_le_php/menu.php";
    ?>
    <!-- Contenu de la page principale pour l'Astronomie -->
    <div class="cards">
        <div class="card">
            <div class="card-banner">
                <p class="category-tag popular">
                    <?php echo $title ?>
                </p>
                <img class="banner-img" src="../../uploads/<?php echo $filename; ?>" alt="">
            </div>
            <div class="card-body">
                <!-- <p class="blog-hashtag">#webdevelopment #frontend</p> -->
                <h2 class="blog-title">
                    <?php echo $title_contenu ?>
                </h2>
                <div class="card-profile">
                    <!-- <img class="profile-img"
                            src='https://images.unsplash.com/photo-1554780336-390462301acf?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ'
                            alt=''> -->
                    <div class="card-profile-info">
                        <h3 class="profile-name">
                            Date :
                            <?php echo date("d/m/Y H:i:s", strtotime($date_astronomie)) ?>
                        </h3>
                        <h3 class="profile-name">
                            Auteur :
                            <?php echo $name ?>
                        </h3>
                        <!-- <p class="profile-followers">1.2k followers</p> -->
                    </div>
                </div>
            </div>
            <hr>
            <div class="card-body-contenu">
                <p class="blog-description">
                    <?php echo $contenu ?>
                </p>
            </div>
        </div>
    </div>


    <script src="script.js"></script>

    <?php
    include "../../cookie/cookie.php";
    ?>
    <?php
    include "../../import_dans_le_php/footer.php";
    ?>

    <script src="../app.js"></script>
</body>

</html>