<?php require_once "controllerUserData.php"; ?>
<?php
$email = $_SESSION['email'];
if ($email == false) {
    header('Location: login-user.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Créer un nouveau mot de passe</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form action="new-password.php" method="POST" autocomplete="off">
                    <h2 class="text-center">Nouveau mot de passe</h2>
                    <?php
                    if (isset($_SESSION['info'])) {
                        ?>
                        <div class="alert alert-success text-center">
                            <?php echo $_SESSION['info']; ?>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if (count($errors) > 0) {
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach ($errors as $showerror) {
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group mb-2">
                        <input class="form-control" type="password" name="password"
                            placeholder="Créer un nouveau mot de passe" required>
                    </div>
                    <div class="form-group mb-2">
                        <input class="form-control" type="password" name="cpassword"
                            placeholder="Confirmez votre mot de passe" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="change-password" value="Changer">
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>