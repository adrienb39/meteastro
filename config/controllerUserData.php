<?php
// session_start();
require "connexion_bdd.php"; // Assurez-vous que ce fichier établit une connexion PDO
$email = "";
$name = "";
$errors = array();

// Si l'utilisateur clique sur le bouton d'inscription
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'adresse électronique n'est pas valide !";
    }
    if (strlen($password) < 8) {
        $errors['password'] = "La longueur du mot de passe doit être d'au moins 8 caractères !";
    }
    $pattern = '/^(?=.*\d)(?=.*[A-Za-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
    if (!preg_match($pattern, $password)) {
        $errors['password'] = "Le mot de passe doit contenir au moins un chiffre, une lettre majuscule, une lettre minuscule et un caractère spécial.";
    }
    if ($password !== $cpassword) {
        $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
    }

    // Vérification de l'email
    $email_check = "SELECT * FROM usertable WHERE email = :email";
    $stmt = $db->prepare($email_check);
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        $errors['email'] = "L'email que vous avez saisi existe déjà !";
    }

    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111);
        $status = "notverified";

        $insert_data = "INSERT INTO usertable (name, email, password, code, status)
                        VALUES (:name, :email, :password, :code, :status)";
        $stmt = $db->prepare($insert_data);
        $data_check = $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $encpass,
            'code' => $code,
            'status' => $status,
        ]);

        if ($data_check) {
            $subject = "Code de vérification de l'email";
            $message = "Votre code de vérification est $code";
            $sender = "From: meteastro@astrometech.com";
            if (mail($email, $subject, $message, $sender)) {
                $info = "Nous avons envoyé un code de vérification à votre adresse électronique. - $email";
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

// Si l'utilisateur clique sur le bouton de soumission du code de vérification
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code = "SELECT * FROM usertable WHERE code = :code";
    $stmt = $db->prepare($check_code);
    $stmt->execute(['code' => $otp_code]);
    
    if ($stmt->rowCount() > 0) {
        $fetch_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'verified';

        $update_otp = "UPDATE usertable SET code = :code, status = :status WHERE code = :fetch_code";
        $stmt = $db->prepare($update_otp);
        $update_res = $stmt->execute(['code' => $code, 'status' => $status, 'fetch_code' => $fetch_code]);

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

// Si l'utilisateur clique sur le bouton de connexion
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $check_email = "SELECT * FROM usertable WHERE email = :email";
    $stmt = $db->prepare($check_email);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        $fetch_pass = $fetch['password'];
        if (password_verify($password, $fetch_pass)) {
            $_SESSION['email'] = $email;
            $status = $fetch['status'];
            if ($status == 'verified') {
                $_SESSION['password'] = $password;
                header('location: /index-connect.php');
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

// Si l'utilisateur clique sur le bouton "continuer" dans le formulaire de mot de passe oublié
if (isset($_POST['check-email'])) {
    $email = $_POST['email'];
    $check_email = "SELECT * FROM usertable WHERE email = :email";
    $stmt = $db->prepare($check_email);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $code = rand(999999, 111111);
        $insert_code = "UPDATE usertable SET code = :code WHERE email = :email";
        $stmt = $db->prepare($insert_code);
        $run_query = $stmt->execute(['code' => $code, 'email' => $email]);

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

// Si l'utilisateur clique sur le bouton de vérification du code de réinitialisation
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code = "SELECT * FROM usertable WHERE code = :code";
    $stmt = $db->prepare($check_code);
    $stmt->execute(['code' => $otp_code]);

    if ($stmt->rowCount() > 0) {
        $fetch_data = $stmt->fetch(PDO::FETCH_ASSOC);
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

// Si l'utilisateur clique sur le bouton de changement de mot de passe
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    if ($password !== $cpassword) {
        $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
    } else {
        $code = 0;
        $email = $_SESSION['email']; // Récupération de cet email via la session
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $update_pass = "UPDATE usertable SET code = :code, password = :password WHERE email = :email";
        $stmt = $db->prepare($update_pass);
        $run_query = $stmt->execute(['code' => $code, 'password' => $encpass, 'email' => $email]);
        
        if ($run_query) {
            $info = "Votre mot de passe a changé. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
            $_SESSION['info'] = $info;
            header('Location: password-changed.php');
        } else {
            $errors['db-error'] = "Le changement de mot de passe a échoué !";
        }
    }
}

// Si l'utilisateur clique sur le bouton "se connecter maintenant"
if (isset($_POST['login-now'])) {
    header('Location: login.php');
}