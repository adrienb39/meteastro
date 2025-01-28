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
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="connexion.css" />
    <link rel="stylesheet" href="information/information.css" />
    <title>Meteastro : Astronomie / meteorologie</title>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form class="box sign-in-form" action="reset-code.php" method="post" name="login">
                    <h2 class="box-title title">VÉRIFICATION</h2>
                    <div class="input-field">
                        <i class="fas fa-check"></i>
                        <input type="number" class="box-input" name="otp" placeholder="Saisir le code de vérification"
                            required>
                    </div>
                    <input type="submit" value="Soumettre" name="check-reset-otp" class="box-button btn solid">
                    <?php
                    if (isset($_SESSION['info'])) {
                        ?>
                        <div style="color: red; text-align: center; padding: 0 100px;">
                            <?php echo $_SESSION['info']; ?>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if (count($errors) > 0) {
                        ?>
                        <div style="color: red; text-align: center; padding: 0 100px;">
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

        <script src="login.js"></script>
</body>

</html>