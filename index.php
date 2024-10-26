<?php
session_start();
require_once 'config/connexion_bdd.php';

$dbType = 'pdo';

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
    <meta name="keywords" content="meteastro, astronomie, meteorologie">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="icon" type="image/png" sizes="16x16" href="/ressources/logo.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="CSS/style.css" />

    <style>
        .blc-astro-meteo {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .blc-astronomie {
            border-radius: 20px;
            /* background-color: floralwhite; */
            background: url(ressources/IMG_0191.JPG);
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            width: 0;
            height: 0;
            overflow: hidden;
            box-shadow: black 0 0 15px;
            padding: 150px 150px;
        }

        .blc-meteorologie {
            border-radius: 20px;
            /* background-color: floralwhite; */
            background: url(ressources/IMG_0933.jpg);
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            width: 0;
            height: 0;
            overflow: hidden;
            box-shadow: black 0 0 15px;
            padding: 150px 150px;
        }

        .title-astronomie {
            display: block;
            width: 300px;
            font-size: 32px;
            color: white;
            font-weight: bold;
            margin-left: -150px;
            margin-top: -105px;
        }

        .title-astronomie::after {
            content: '';
            display: block;
            width: 60%;
            height: 1px;
            border-top: 1px solid white;
            margin: 3rem 60px;
        }

        .title-meteorologie {
            display: block;
            width: 300px;
            font-size: 32px;
            color: black;
            font-weight: bold;
            margin-left: -150px;
            margin-top: -105px;
        }

        .title-meteorologie::after {
            content: '';
            display: block;
            width: 60%;
            height: 1px;
            border-top: 1px solid black;
            margin: 3rem 60px;
        }

        .p-astronomie {
            display: block;
            width: 300px;
            height: auto;
            color: white;
            margin-left: -150px;
            margin-top: 50px;
        }

        .p-meteorologie {
            display: block;
            width: 300px;
            height: auto;
            color: black;
            margin-left: -150px;
            margin-top: 50px;
        }

        /* .blc-astronomie:hover {
            background: url(ressources/IMG_0191.JPG);
            background-size: cover;
        } */

        .blc-astronomie:hover .title-astronomie,
        .blc-astronomie:hover .title-astronomie::after,
        .blc-astronomie:hover .p-astronomie {
            color: white;
        }

        /* .blc-meteorologie:hover {
            background: url(ressources/IMG_0933.jpg);
            background-size: cover;
        } */

        /* responsive */
        @media screen and (max-width: 1300px) {
            .blc-astro-meteo {
                display: flex;
                flex-direction: column;
            }

            .blc-astronomie {
                margin-top: 20px;
            }

            .blc-meteorologie {
                margin-top: 20px
            }
        }
    </style>

    <style>
        .news {
            max-width: 700px;
            width: 100%;
            margin: 100px auto;
            padding: 25px 30px 30px 30px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
        }

        .darkmode .news {
            max-width: 700px;
            width: 100%;
            margin: 100px auto;
            padding: 25px 30px 30px 30px;
            border-radius: 20px;
            background: var(--night-300);
            box-shadow: white 0 0 10px;
        }

        .news header {
            font-size: 30px;
            font-weight: 600;
            padding-bottom: 20px;
            color: black;
        }

        .darkmode .news header {
            font-size: 30px;
            font-weight: 600;
            padding-bottom: 20px;
            color: white;
        }

        .news header::after {
            content: '';
            display: block;
            width: 20%;
            height: 1px;
            border-top: 1px solid black;
            margin: 3rem 40%;
        }

        .darkmode .news header::after {
            content: '';
            display: block;
            width: 20%;
            height: 1px;
            border-top: 1px solid white;
            margin: 3rem 40%;
        }

        .news nav {
            position: relative;
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
        }

        .news nav label {
            display: block;
            height: 100%;
            width: 100%;
            text-align: center;
            line-height: 50px;
            cursor: pointer;
            position: relative;
            z-index: 1;
            color: red;
            font-size: 17px;
            border-radius: 5px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .news nav label:hover {
            background: red;
            color: white;
        }

        #astronomie:checked~nav label.astronomie,
        #meteorologie:checked~nav label.meteorologie {
            color: #fff;
        }

        nav label i {
            padding-right: 7px;
        }

        nav .slider {
            position: absolute;
            height: 100%;
            width: 50%;
            left: 0;
            bottom: 0;
            z-index: 0;
            border-radius: 5px;
            background: red;
            transition: all 0.3s ease;
        }

        input[type="radio"] {
            display: none;
        }

        #meteorologie:checked~nav .slider {
            left: 50%;
        }

        section .content {
            display: none;
        }

        #astronomie:checked~section .content-1,
        #meteorologie:checked~section .content-2 {
            display: block;
        }

        section .content .title {
            font-size: 21px;
            font-weight: 500;
            margin: 30px 0 10px 0;
            color: black;
            text-decoration: underline overline;
        }

        .darkmode section .content .title {
            font-size: 21px;
            font-weight: 500;
            margin: 30px 0 10px 0;
            color: white;
            text-decoration: underline overline;
        }

        section .content p {
            text-align: justify;
            color: black;
        }

        .darkmode section .content p {
            text-align: justify;
            color: white;
        }

        /* responsive */
        @media screen and (max-width: 800px) {
            .news {
                margin-left: -4px;
                padding: 4px;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js">
    </script>
</head>

<body>
    <?php
    include "import_dans_le_php/menu.php";
    ?>
    <!-- contenu de la page principale -->
    <main class="main">
        <section id="accueil">
            <!-- <h1>
                <a class="typewrite" data-period="10000"
                    data-type='[ "Bienvenue sur le site de Meteastro !", "slogan : Meteastro vise les étoiles" ]'>
                    <span class="wrap"></span>
                </a>
            </h1>
            <img class="logo_meteastro" src="/ressources/logo.png" alt="logo de Meteastro"> -->
            <div class="blc-astro-meteo">
                <div class="blc-astronomie">
                    <a class="title-astronomie" href="/divers/astronomie/astronomie.php">Astronomie</a>
                    <p class="p-astronomie">Ce site comprend une page dédiée à l'astronomie avec des articles, des
                        événements, des news,...</p>
                </div>
                <div class="blc-meteorologie">
                    <a class="title-meteorologie" href="/divers/meteorologie/meteorologie.php">Météorologie</a>
                    <p class="p-meteorologie">Ce site comprend une page dédiée à la météorologie avec des articles, des
                        événements, des news,...</p>
                </div>
            </div>
            <div class="news">
                <header>Dernières nouvelles</header>
                <input type="radio" name="slider-news" checked id="astronomie">
                <input type="radio" name="slider-news" id="meteorologie">
                <nav>
                    <label for="astronomie" class="astronomie">Astronomie</label>
                    <label for="meteorologie" class="meteorologie">Météorologie</label>
                    <div class="slider"></div>
                </nav>
                <section>
                    <div class="content content-1">
                        <?php
                        function afficherContenu($db, $table)
                        {
                            $sql = "SELECT * FROM $table WHERE verified='y' ORDER BY id DESC LIMIT 1";

                            try {
                                $stmt = $db->prepare($sql);
                                $stmt->execute();
                                $contenus = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère tous les résultats sous forme de tableau
                        
                                if ($contenus) {
                                    foreach ($contenus as $row) {
                                        echo "<div class='title'>" . htmlspecialchars($row["title_contenu"]) . "</div>";
                                        if (strlen($row["contenu"]) > 10) {
                                            $contenu = substr($row["contenu"], 0, 100);
                                            $contenu = substr($contenu, 0, strrpos($contenu, ' '));
                                            echo "<p>" . htmlspecialchars($contenu) . "... <a style='color: red;' href='/divers/$table/$table.php'>Lire plus</a></p>";
                                        }
                                    }
                                } else {
                                    echo "0 résultat";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . $e->getMessage();
                            }
                        }

                        // Assure-toi que $pdo est bien ta connexion PDO
                        afficherContenu($db, 'astronomie');
                        ?>
                    </div>
                    <div class="content content-2">
                        <?php
                        afficherContenu($db, 'meteorologie');
                        ?>
                    </div>

                </section>
            </div>
            </div>
        </section>
        <!-- <section id="identifier"></section> -->
        <section id="contacts">
            <fieldset>
                <legend>🪐CONTACT🪐</legend>
                <div class="contener-contacts">
                    <form action="contacts.php" method="post">
                        <div class="blc-contact">
                            <label for="pseudo">Entrez un pseudo</label>
                            <input class="contact" type="text" id="pseudo" name="pseudo" placeholder="pseudo" required
                                data-msg>
                        </div>
                        <div class="blc-contact">
                            <label for="nom">Votre nom et prénom</label>
                            <input class="contact" type="text" id="nom" name="nom" placeholder="nom" required data-msg>
                            <input class="contact" type="text" id="prenom" name="prenom" placeholder="prénom" required
                                data-msg>
                        </div>
                        <div class="blc-contact">
                            <label for="email">Votre email</label>
                            <input class="contact" type="email" id="email" name="email" placeholder="email" required
                                data-msg>
                        </div>
                        <!-- <div class="blc-contact" style="display: none;">
                            <select name="choix" id="choix" required data-msg>
                                <option value="contacts">Contacts</option>
                            </select>
                        </div> -->
                        <div class="blc-contact">
                            <label for="message">Votre message</label>
                            <textarea class="contact-message" id="message" name="message"
                                placeholder="Entrez un message" required></textarea>
                        </div>
                        <div class="blc-btn">
                            <button class="btn" type="submit">Envoyer</button>
                        </div>
                    </form>
                </div>
            </fieldset>
        </section>
    </main>
    <?php
    include "cookie/cookie.php"
        ?>
    <?php
    include "import_dans_le_php/footer.php";
    ?>

    <script src="app.js"></script>
</body>

</html>