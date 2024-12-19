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

    <style>
    /* Ciel étoilé animé */
    #stars-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    /* Créer des étoiles animées */
    .star {
        position: absolute;
        border-radius: 50%;
        background-color: white;
        animation: twinkle 1.5s infinite ease-in-out;
    }

    /* Positionner aléatoirement les étoiles */
    #stars-background .star:nth-child(1) { top: 20%; left: 10%; width: 2px; height: 2px; animation-duration: 2s; }
    #stars-background .star:nth-child(2) { top: 50%; left: 30%; width: 3px; height: 3px; animation-duration: 2.5s; }
    #stars-background .star:nth-child(3) { top: 80%; left: 70%; width: 1px; height: 1px; animation-duration: 1.2s; }
    #stars-background .star:nth-child(4) { top: 40%; left: 90%; width: 2px; height: 2px; animation-duration: 1.7s; }
    #stars-background .star:nth-child(5) { top: 60%; left: 20%; width: 4px; height: 4px; animation-duration: 3s; }

    /* Animation pour le scintillement des étoiles */
    @keyframes twinkle {
        0% { opacity: 0.7; }
        50% { opacity: 1; }
        100% { opacity: 0.7; }
    }

    /* Styles pour la bannière */
    #cookie-banner p {
        margin: 5px 0;
    }

    #cookie-banner a {
        color: #fff;
        text-decoration: underline;
    }

    #cookie-banner button {
        background-color: #555;
        border: none;
        padding: 8px 16px;
        color: #fff;
        cursor: pointer;
        margin-top: 10px;
    }

    #cookie-banner button:hover {
        background-color: #444;
    }
</style>
</head>

<body>
    <?php if (isset($_SESSION['email']) && isset($_SESSION['password'])) { ?>
        <div class="container">
            <div class="forms-container">
                <div class="signin-signup">
                    <form class="box sign-in-form" action="" method="post" name="login" novalidate>
                        <h2 class="box-title title">INSCRIPTION</h2>
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
                    <form class="box sign-in-form" action="signup.php" method="post" name="signup" novalidate>
                        <h2 class="box-title title">INSCRIPTION</h2>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" class="box-input" name="name" placeholder="Nom complet" required
                                value="<?php echo htmlspecialchars($name); ?>">
                        </div>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="text" class="box-input" name="email" placeholder="Email" required
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="box-input" name="password" placeholder="Mot de passe" required />
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="box-input" name="cpassword"
                                placeholder="Confirmation de votre mot de passe" required />
                        </div>
                        <div>
                            <input type="checkbox" name="consent" id="consent" required />
                            <label for="consent">J'accepte les <a href="terms.php" target="_blank">termes et
                                    conditions</a>.</label>
                        </div>
                        <input type="submit" name="signup" value="S'inscrire" class="box-button btn" />
                        <?php
                        if (count($errors) == 1) {
                            ?>
                            <div style="color: red; text-align: center; padding: 0 100px;">
                                <?php
                                foreach ($errors as $showerror) {
                                    echo htmlspecialchars($showerror);
                                    echo PHP_EOL;
                                }
                                ?>
                            </div>
                            <?php
                        } elseif (count($errors) > 1) {
                            ?>
                            <div style="color: red; text-align: center; padding: 0 100px;">
                                <ul>
                                    <?php
                                    foreach ($errors as $showerror) {
                                        ?>
                                        <li><?php echo htmlspecialchars($showerror); ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
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
                        <h3>Déjà inscrit ?</h3>
                        <p>
                            Si vous êtes déjà inscrit, vous pouvez vous connecter pour accéder aux fonctionnalités et aux
                            contenus supplémentaires !
                        </p>
                        <a class="btn transparent" href="login.php">
                            CONNEXION
                        </a>
                    </div>
                    <img src="https://i.ibb.co/nP8H853/Mobile-login-rafiki.png" class="image" alt="" />
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