<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="connexion.css" />
    <title>Meteastro : Astronomie / meteorologie</title>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form class="box sign-in-form" action="login-connexion.php" method="post" name="login">
                    <h2 class="box-title title">CONNEXION</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" class="box-input" name="username" placeholder="Nom d'utilisateur">
                    </div>
                    <select style="display: none;" name="type" class="box-input">
                        <option value="INSCRIT">INSCRIT</option>
                    </select>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="box-input" name="password" placeholder="Mot de passe">
                    </div>
                    <input type="submit" value="Se connecter " name="submit" class="box-button btn solid">
                    <?php if (!empty($message)) { ?>
                        <p class="errorMessage">
                            <?php echo $message; ?>
                        </p>
                    <?php } ?>
                    <p class="social-text">Revenir sur la page d'accueil du Site Web : <a
                            style="text-decoration: none; color: red;" href="/index.php">Accueil du
                            Site Web</a></p>
                </form>
                <form class="box sign-up-form" action="login-inscription.php" method="post" name="login">
                    <h2 class="box-title title">INSCRIPTION</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" class="box-input" name="nom" placeholder="Nom" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" class="box-input" name="prenom" placeholder="Prénom" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" class="box-input" name="username" placeholder="Nom d'utilisateur" required />
                    </div>
                    <select style="display: none;" class="box-input" name="type">
                        <option value="INSCRIT">INSCRIT</option>
                    </select>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" class="box-input" name="email" placeholder="Email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="box-input" name="password" placeholder="Mot de passe" required />
                    </div>
                    <input type="submit" name="submit" value="S'inscrire" class="box-button btn" />
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

    <script src="login.js"></script>
</body>

</html>