<?php
/* $bdd = new PDO('mysql:host=localhost;dbname=meteastro', 'root', 'Robot500');

if (isset($_POST['forminscription'])) {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $mail = htmlspecialchars($_POST['mail']);
    $mail2 = htmlspecialchars($_POST['mail2']);
    $mdp = sha1($_POST['mdp']);
    $mdp2 = sha1($_POST['mdp2']);
    if (!empty($_POST['pseudo']) and !empty($_POST['mail']) and !empty($_POST['mail2']) and !empty($_POST['mdp']) and !empty($_POST['mdp2'])) {
        $pseudolength = strlen($pseudo);
        if ($pseudolength <= 255) {
            if ($mail == $mail2) {
                if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    $reqmail = $bdd->prepare("SELECT * FROM membres WHERE mail = ?");
                    $reqmail->execute(array($mail));
                    $mailexist = $reqmail->rowCount();
                    if ($mailexist == 0) {
                        if ($mdp == $mdp2) {
                            $insertmbr = $bdd->prepare("INSERT INTO membres(pseudo, mail, motdepasse) VALUES(?, ?, ?)");
                            $insertmbr->execute(array($pseudo, $mail, $mdp));
                            $erreur = "Votre compte a bien été créé !";
                        } else {
                            $erreur = "Vos mots de passes ne correspondent pas !";
                        }
                    } else {
                        $erreur = "Adresse mail déjà utilisée !";
                    }
                } else {
                    $erreur = "Votre adresse mail n'est pas valide !";
                }
            } else {
                $erreur = "Vos adresses mail ne correspondent pas !";
            }
        } else {
            $erreur = "Votre pseudo ne doit pas dépasser 255 caractères !";
        }
    } else {
        $erreur = "Tous les champs doivent être complétés !";
    }
}
?>
<?php
session_start();

$bdd = new PDO('mysql:host=localhost;dbname=meteastro', 'root', 'Robot500');

if (isset($_POST['formconnexion'])) {
    $mailconnect = htmlspecialchars($_POST['mailconnect']);
    $mdpconnect = sha1($_POST['mdpconnect']);
    if (!empty($mailconnect) and !empty($mdpconnect)) {
        $requser = $bdd->prepare("SELECT * FROM membres WHERE mail = ? AND motdepasse = ?");
        $requser->execute(array($mailconnect, $mdpconnect));
        $userexist = $requser->rowCount();
        if ($userexist == 1) {
            $userinfo = $requser->fetch();
            $_SESSION['id'] = $userinfo['id'];
            $_SESSION['pseudo'] = $userinfo['pseudo'];
            $_SESSION['mail'] = $userinfo['mail'];
            header("Location: ../index-connect.php?id=" . $_SESSION['id']);
        } else {
            $erreur = "Mauvais mail ou mot de passe !";
        }
    } else {
        $erreur = "Tous les champs doivent être complétés !";
    }
} */
// require_once "../config/connexion_bdd.php";
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
                    <form class="box sign-up-form" action="login.php" method="post" name="login" novalidate>
                        <h2 class="box-title title">INSCRIPTION</h2>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" class="box-input" name="name" placeholder="Nom complet" required
                                value="<?php echo $name ?>">
                        </div>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="text" class="box-input" name="email" placeholder="Email" required
                                value="<?php echo $email ?>">
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
                        <input type="submit" name="signup" value="S'inscrire" class="box-button btn" />
                        <?php
                        if (count($errors) == 1) {
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
                        } elseif (count($errors) > 1) {
                            ?>
                            <div style="color: red; text-align: center; padding: 0 100px;">
                                <?php
                                foreach ($errors as $showerror) {
                                    ?>
                                    <li>
                                        <?php echo $showerror; ?>
                                    </li>
                                    <?php
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
                        <button class="btn transparent" id="sign-up-btn">
                            INSCRIPTION
                        </button>
                    </div>
                    <img src="https://i.ibb.co/6HXL6q1/Privacy-policy-rafiki.png" class="image" alt="" />
                </div>
                <div class="panel right-panel">
                    <div class="content">
                        <h3>Déjà inscrit ?</h3>
                        <p>
                            Si vous êtes déjà inscrit, vous pouvez vous connecter pour accéder au fonctionnalités et au
                            contenus supplémentaire !
                        </p>
                        <button class="btn transparent" id="sign-in-btn">
                            CONNEXION
                        </button>
                    </div>
                    <img src="https://i.ibb.co/nP8H853/Mobile-login-rafiki.png" class="image" alt="" />
                </div>
            </div>
        </div>
    <?php } ?>

    <script src="login.js"></script>

</body>

</html>