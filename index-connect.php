<?php require_once "connexion/controllerUserData.php"; ?>
<?php
$email = $_SESSION['email'];
$password = $_SESSION['password'];
if ($email != false && $password != false) {
    $sql = "SELECT * FROM usertable WHERE email = '$email'";
    $run_Sql = mysqli_query($con, $sql);
    if ($run_Sql) {
        $fetch_info = mysqli_fetch_assoc($run_Sql);
        $status = $fetch_info['status'];
        $code = $fetch_info['code'];
        if ($status == "verified") {
            if ($code != 0) {
                header('Location: connexion/reset-code.php');
            }
        } else {
            header('Location: connexion/user-otp.php');
        }
    }
} else {
    header('Location: connexion/login.php');
}
?>
<?php
/* session_start();

$bdd = new PDO('mysql:host=localhost;dbname=meteastro', 'root', 'Robot500');

if (isset($_GET['id']) and $_GET['id'] > 0) {
    $getid = intval($_GET['id']);
    $requser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
    $requser->execute(array($getid));
    $userinfo = $requser->fetch(); */
?>
<?php
require_once 'config/connexion_bdd.php';
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
    <link rel="stylesheet" href="divers/divers.css">

    <style>
        .blc-astro-meteo {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .blc-astronomie {
            border-radius: 20px;
            background-color: floralwhite;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            width: 0;
            height: 0;
            overflow: hidden;
            box-shadow: black 0 0 15px;
            padding: 150px 150px;
        }

        .blc-meteorologie {
            border-radius: 20px;
            background-color: floralwhite;
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
            color: black;
            font-weight: bold;
            margin-left: -150px;
            margin-top: -105px;
        }

        .title-astronomie::after {
            content: '';
            display: block;
            width: 60%;
            height: 1px;
            border-top: 1px solid black;
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
            color: black;
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

        .blc-astronomie:hover {
            background: url(ressources/IMG_0191.JPG);
            background-size: cover;
        }

        .blc-astronomie:hover .title-astronomie,
        .blc-astronomie:hover .title-astronomie::after,
        .blc-astronomie:hover .p-astronomie {
            color: white;
        }

        .blc-meteorologie:hover {
            background: url(ressources/IMG_0933.jpg);
            background-size: cover;
        }

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

        .news header {
            font-size: 30px;
            font-weight: 600;
            padding-bottom: 20px;
            color: black;
        }

        .news header::after {
            content: '';
            display: block;
            width: 20%;
            height: 1px;
            border-top: 1px solid black;
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
            background: #fff;
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

        section .content p {
            text-align: justify;
            color: black;
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
    include "import_dans_le_php/menu-connect.php";
    ?>
    <!-- contenu de la page principale -->
    </div>
    <main class="main">
        <section id="accueil">
            <h1>
                <a class="typewrite" data-period="10000"
                    data-type='[ "Bienvenue sur le site de Meteastro !", "slogan : Meteastro vise les étoiles" ]'>
                    <span class="wrap"></span>
                </a>
            </h1>
            <div class="marquee">
                <div class="info" style="color: red; font-weight: bold;">
                    <?php echo $fetch_info['avertissement']; ?>
                </div>
            </div>
            <img class="logo_meteastro" src="/ressources/logo.png" alt="logo de Meteastro">
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
                        $sql = "SELECT * FROM astronomie ORDER BY id DESC LIMIT 1";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Afficher les résultats de chaque ligne
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='title'>" . $row["title"] . "</div>
                    <p>" . $row["contenu"] . "</p>";
                            }
                        } else {
                            echo "0 resultat";
                        }

                        ?>
                    </div>
                    <div class="content content-2">
                        <?php
                        $sql = "SELECT * FROM meteorologie ORDER BY id DESC LIMIT 1";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Afficher les résultats de chaque ligne
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='title'>" . $row["title"] . "</div>
                    <p>" . $row["contenu"] . "</p>";
                            }
                        } else {
                            echo "0 resultat";
                        }

                        ?>
                    </div>
                </section>
            </div>
            </div>
        </section>
        <section id="astronomie">
            <h1>ASTRONOMIE</h1>
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

                    ?>
                </div>
            </div>
        </section>
        <section id="meteorologie">
            <h1>MÉTÉOROLOGIE</h1>
            <!-- Contenu de la page principale pour la Météorologie -->
            <div class="navbar-onglets">
                <label class="label-onglets" for="toggle">☰</label>
                <input type="checkbox" id="toggle">
                <div class="container-onglets">
                    <?php
                    $sql = "SELECT * FROM meteorologie";
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
        </section>
        <section id="contacts">
            <fieldset>
                <legend>contact</legend>
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
                        <div class="blc-contact" style="display: none;">
                            <select name="choix" id="choix" required data-msg>
                                <option value="contacts">Contacts</option>
                            </select>
                        </div>
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
    <script src="divers/app.js"></script>

</body>

</html>