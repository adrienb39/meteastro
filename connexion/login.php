<?php

require_once "../config/controllerUserData.php";

?>

<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="connexion.css" />
    <link rel="stylesheet" href="information/information.css" />
    <title>Meteastro : Astronomie / meteorologie</title>
</head>

<body>
    <?php if (isset($_SESSION['email']) && isset($_SESSION['password'])) { ?>
        <div class="container">
            <div class="forms-container">
                <div class="signin-signup">
                    <form class="box sign-in-form" action="" method="post" name="login" novalidate>
                        <h2 class="box-title title">CONNEXION</h2>
                        <div class="input-field-session">Vous êtes déjà connecté ! Vous voulez vous déconnecter ?
                        </div>
                        <a class="box-button btn-session solid" href="logout.php">Déconnexion</a>
                    </form>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="container">
            <div class="forms-container">
                <div class="signin-signup">

                    <form class="box sign-in-form" action="login.php" method="post" name="login" novalidate>
                        <h2 class="box-title title">CONNEXION</h2>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="email" class="box-input" name="email" placeholder="Email"
                                value="<?php echo $email ?>">
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="box-input" name="password" placeholder="Mot de passe">
                        </div>
                        <input type="submit" value="Se connecter " name="login" class="box-button btn solid">
                        <div><a style="text-decoration: none; color: red;" href="forgot-password.php">Mot de passe oublié
                                ?</a>
                        </div>
                        <?php
                        if (count($errors) > 0) {
                            ?>
                            <div style="color: red; text-align: center; padding: 0 100px;">
                                <?php
                                foreach ($errors as $showerror) {
                                    echo $showerror;
                                    echo PHP_EOL;
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                        <p class="social-text">Revenir sur la page d'accueil du Site Web : <a
                                style="text-decoration: none; color: red;" href="/index.php">Accueil du
                                Site Web</a></p>
                    </form>
                </div>
            </div>

            <div class="panels-container">
                <div class="panel left-panel">
                    <div class="content">
                        <h3>Nouveau ici ?</h3>
                        <p>
                            Inscrivez-vous pour pouvoir accéder au fonctionnalités et au contenus supplémentaire !
                        </p>
                        <a class="btn transparent" href="signup.php">
                            INSCRIPTION
                        </a>
                    </div>
                    <img src="https://i.ibb.co/6HXL6q1/Privacy-policy-rafiki.png" class="image" alt="" />
                </div>
            </div>
        </div>
    <?php } ?>
    <div id="cookie-banner"
        style="position: fixed; bottom: 0; width: 100%; background-color: #333; color: #fff; padding: 10px; text-align: center; z-index: 1000;">
        <div id="stars-background"></div>
        <p>Une nouvelle version majeure (version 4) de la page de connexion/inscription sera bientôt disponible. Restez
            à l'affût !</p>
    </div>

    <script src="login.js"></script>

</body>

</html>