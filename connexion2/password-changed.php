<?php require_once "controllerUserData.php"; ?>
<?php
if ($_SESSION['info'] == false) {
    header('Location: login-user.php');
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form login-form">
                <?php
                if (isset($_SESSION['info'])) {
                    ?>
                    <div class="alert alert-success text-center">
                        <?php echo $_SESSION['info']; ?>
                    </div>
                    <?php
                }
                ?>
                <form action="login-user.php" method="POST">
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="login-now"
                            value="Se connecter maintenant">
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>