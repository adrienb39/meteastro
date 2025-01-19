<?php require_once "../config/controllerUserData.php"; ?>

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
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form class="box sign-in-form" action="forgot-password.php" method="post" name="login">
                    <h2 class="box-title title">MOT DE PASSE OUBLIÉ</h2>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" class="box-input" name="email" placeholder="Email" required
                            value="<?php echo $email ?>">
                    </div>
                    <input type="submit" value="Continuer" name="check-email" class="box-button btn solid">
                    <?php
                    if (count($errors) > 0) {
                        ?>
                        <div style="color: red; text-align: center; padding: 0 100px;">
                            <?php
                            foreach ($errors as $error) {
                                echo $error;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </div>

        <script src="login.js"></script>
</body>

</html>