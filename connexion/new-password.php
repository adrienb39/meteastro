<?php require_once "../config/controllerUserData.php"; ?>
<?php
$email = $_SESSION['email'];
if ($email == false) {
    header('Location: login.php');
}
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
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form class="box sign-in-form" action="new-password.php" method="post" name="login">
                    <h2 class="box-title title">MOT DE PASSE</h2>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="box-input" name="password"
                            placeholder="Créer un nouveau mot de passe" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="box-input" name="cpassword"
                            placeholder="Confirmez votre mot de passe" required>
                    </div>
                    <input type="submit" value="Changer" name="change-password" class="box-button btn solid">
                    <?php
                    if (isset($_SESSION['info'])) {
                        ?>
                        <div style="color: red; text-align: center;">
                            <?php echo $_SESSION['info']; ?>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if (count($errors) > 0) {
                        ?>
                        <div style="color: red; text-align: center;">
                            <?php
                            foreach ($errors as $showerror) {
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </div>
        <div class="planet"></div>
        <div class="asteroid"></div>

        <?php include "about.php"; ?>

        <script src="login.js"></script>
</body>

</html>