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
    <title>Meteastro : Astronomie / meteorologie</title>
</head>

<body>
    <div class="star-field"></div>
    <div class="glowing-stars"></div>
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
                            <label for="consent">J'accepte les <a style="text-decoration: none; color: red;"
                                    href="terms.php" target="_blank">termes et conditions</a>.</label>
                        </div>
                        <input type="submit" name="signup" value="S'inscrire" class="box-button btn" />
                        <div style="display: block ruby;">
                            <h3>Déjà inscrit ? </h3>
                            <a style="text-decoration: none; color: red;" class="btn transparent" href="login.php">
                                CONNEXION
                            </a>
                        </div>
                        <?php
                        if (count($errors) == 1) {
                            ?>
                            <div style="color: red; text-align: center;">
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
                            <div style="color: red; text-align: center;">
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
        </div>
    <?php } ?>
    <div class="planet"></div>
    <div class="asteroid"></div>

    <?php include "about.php"; ?>

    <script src="login.js"></script>

</body>

</html>