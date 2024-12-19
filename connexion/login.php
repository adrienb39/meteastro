<?php

require_once "../config/controllerUserData.php";

?>

<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="connexion.css" />
    <link rel="stylesheet" href="information/information.css" />
    <title>Meteastro : Astronomie / Météorologie</title>
</head>

<body>
    <div class="star-field"></div>
    <div class="glowing-stars"></div>
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
                        <i class="fa-solid fa-user"></i>
                            <input type="email" class="box-input" name="email" placeholder="Email"
                                value="<?php echo $email ?>">
                        </div>
                        <div class="input-field">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" class="box-input" name="password" placeholder="Mot de passe">
                        </div>
                        <input type="submit" value="Se connecter " name="login" class="box-button btn solid">
                        <div><a style="text-decoration: none; color: red;" href="forgot-password.php">Mot de passe oublié
                                ?</a>
                        </div>
                        <div style="display: block ruby;">
                            <h3>Nouveau ici ? </h3>
                            <a style="text-decoration: none; color: red;" class="btn transparent" href="signup.php">
                                INSCRIPTION
                            </a>
                        </div>
                        <?php
                        if (count($errors) > 0) {
                            ?>
                            <div style="color: red; text-align: center;">
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
        </div>
    <?php } ?>
    <div class="planet"></div>
    <div class="asteroid"></div>

    <?php include "about.php"; ?>

    <script src="login.js"></script>

</body>

</html>