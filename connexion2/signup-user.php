<?php require_once "controllerUserData.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form action="signup-user.php" method="POST" autocomplete="">
                    <h2 class="text-center">Inscription</h2>
                    <p class="text-center">C'est simple et rapide.</p>
                    <?php
                    if (count($errors) == 1) {
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach ($errors as $showerror) {
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    } elseif (count($errors) > 1) {
                        ?>
                        <div class="alert alert-danger">
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
                    <div class="form-group mb-2">
                        <input class="form-control" type="text" name="name" placeholder="Nom complet" required
                            value="<?php echo $name ?>">
                    </div>
                    <div class="form-group mb-2">
                        <input class="form-control" type="email" name="email" placeholder="Adresse électronique"
                            required value="<?php echo $email ?>">
                    </div>
                    <div class="form-group mb-2">
                        <input class="form-control" type="password" name="password" placeholder="Mot de passe" required>
                    </div>
                    <div class="form-group mb-2">
                        <input class="form-control" type="password" name="cpassword"
                            placeholder="Confirmer le mot de passe" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="signup" value="S'inscrire">
                    </div>
                    <div class="link login-link text-center">Vous êtes déjà membre ? <a href="login-user.php">Se
                            connecter ici</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>