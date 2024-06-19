<?php
session_start();
require "connection.php";
$email = "";
$name = "";
$errors = array();

//if user signup button
if (isset ($_POST['signup'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    if ($password !== $cpassword) {
        $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
    }
    $email_check = "SELECT * FROM usertable WHERE email = '$email'";
    $res = mysqli_query($con, $email_check);
    if (mysqli_num_rows($res) > 0) {
        $errors['email'] = "L'email que vous avez saisi existe déjà !";
    }
    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111);
        $status = "notverified";
        $insert_data = "INSERT INTO usertable (name, email, password, code, status)
                        values('$name', '$email', '$encpass', '$code', '$status')";
        $data_check = mysqli_query($con, $insert_data);
        if ($data_check) {
            $subject = "Code de vérification de l'email";
            // $subject = "Mise à jour de la page de connexion sur le site Meteastro";
            $message = "Votre code de vérification est $code";
            /* $message = "Cher utilisateur de Meteastro,
            
Nous sommes ravis de vous annoncer que la page de connexion subira bientôt une mise à jour pour améliorer votre expérience d’utilisation. Cette mise à niveau s’inscrit dans le cadre de notre engagement continu à fournir un service de qualité.
            
Voici ce que vous pouvez attendre de cette mise à jour :
            
    . La nouvelle page de connexion sera plus moderne et conviviale, avec un design épuré et adaptatif.
    . Elle sera compatible avec tous les appareils, qu’il s’agisse d’un ordinateur, d’une tablette ou d’un smartphone.
    . Sécurité renforcée : Nous avons mis en place des mesures de sécurité supplémentaires pour protéger vos informations personnelles.
    . Fonctionnalités améliorées : Vous bénéficierez d’une expérience de connexion plus fluide et rapide.
    . Si vous êtes déjà inscrit, vous recevrez un e-mail contenant des instructions pour vous réinscrire avec la nouvelle page de connexion. Veuillez suivre ces étapes pour continuer à accéder à votre compte en toute sécurité.
            
Nous vous remercions de votre confiance et de votre fidélité. Restez à l’écoute pour plus de détails sur la date de déploiement de cette mise à jour et pour découvrir le nouveau look de la page de connexion ! Nous sommes impatients de vous offrir une expérience encore meilleure sur Meteastro !
            
Cordialement, L’équipe Meteastro

\"Ce mail a été générer automatiquement, vous ne devez pas répondre à ce mail\""; */
            $sender = "From: meteastro@astrometech.com";
            if (mail($email, $subject, $message, $sender)) {
                $info = "Nous avons envoyé un code de vérification à votre adresse électronique. - $email";
                // $info = "Une information a été envoyer à l'adresse électronique. - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: user-otp.php');
                exit();
            } else {
                $errors['otp-error'] = "Échec lors de l'envoi du code !";
            }
        } else {
            $errors['db-error'] = "Échec lors de l'insertion de données dans la base de données !";
        }
    }

}
//if user click verification code submit button
if (isset ($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'verified';
        $update_otp = "UPDATE usertable SET code = $code, status = '$status' WHERE code = $fetch_code";
        $update_res = mysqli_query($con, $update_otp);
        if ($update_res) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            header('location: /index-connect.php');
            exit();
        } else {
            $errors['otp-error'] = "Échec lors de la mise à jour du code !";
        }
    } else {
        $errors['otp-error'] = "Vous avez saisi un code incorrect !";
    }
}

//if user click login button
if (isset ($_POST['login'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $check_email = "SELECT * FROM usertable WHERE email = '$email'";
    $res = mysqli_query($con, $check_email);
    if (mysqli_num_rows($res) > 0) {
        $fetch = mysqli_fetch_assoc($res);
        $fetch_pass = $fetch['password'];
        if (password_verify($password, $fetch_pass)) {
            $_SESSION['email'] = $email;
            $status = $fetch['status'];
            if ($status == 'verified') {
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: home.php');
            } else {
                $info = "Il semble que vous n'ayez pas encore vérifié votre adresse e-mail. - $email";
                $_SESSION['info'] = $info;
                header('location: user-otp.php');
            }
        } else {
            $errors['email'] = "Courriel ou mot de passe incorrect !";
        }
    } else {
        $errors['email'] = "Il semblerait que vous ne soyez pas encore membre ! Cliquez sur le lien du bas pour vous inscrire.";
    }
}

//if user click continue button in forgot password form
if (isset ($_POST['check-email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_email = "SELECT * FROM usertable WHERE email='$email'";
    $run_sql = mysqli_query($con, $check_email);
    if (mysqli_num_rows($run_sql) > 0) {
        $code = rand(999999, 111111);
        $insert_code = "UPDATE usertable SET code = $code WHERE email = '$email'";
        $run_query = mysqli_query($con, $insert_code);
        if ($run_query) {
            $subject = "Code de réinitialisation du mot de passe";
            $message = "Votre code de réinitialisation du mot de passe est $code";
            $sender = "From: meteastro@astrometech.com";
            if (mail($email, $subject, $message, $sender)) {
                $info = "Nous avons envoyé une demande de réinitialisation de mot de passe à votre adresse électronique. - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                header('location: reset-code.php');
                exit();
            } else {
                $errors['otp-error'] = "Échec lors de l'envoi du code !";
            }
        } else {
            $errors['db-error'] = "Quelque chose n'a pas fonctionné !";
        }
    } else {
        $errors['email'] = "Cette adresse e-mail n'existe pas !";
    }
}

//if user click check reset otp button
if (isset ($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Veuillez créer un nouveau mot de passe que vous n'utilisez sur aucun autre site.";
        $_SESSION['info'] = $info;
        header('location: new-password.php');
        exit();
    } else {
        $errors['otp-error'] = "Vous avez saisi un code incorrect !";
    }
}

//if user click change password button
if (isset ($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    if ($password !== $cpassword) {
        $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
    } else {
        $code = 0;
        $email = $_SESSION['email']; //getting this email using session
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $update_pass = "UPDATE usertable SET code = $code, password = '$encpass' WHERE email = '$email'";
        $run_query = mysqli_query($con, $update_pass);
        if ($run_query) {
            $info = "Votre mot de passe a changé. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
            $_SESSION['info'] = $info;
            header('Location: password-changed.php');
        } else {
            $errors['db-error'] = "Le changement de mot de passe a échoué !";
        }
    }
}

//if login now button click
if (isset ($_POST['login-now'])) {
    header('Location: login-user.php');
}
?>