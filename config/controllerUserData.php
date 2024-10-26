<?php

session_start();
require "connexion_bdd.php"; // Assurez-vous que ce fichier contient la fonction createMysqliConnection()

$dbType = 'mysqli';

if ($dbType === 'pdo') {
    $db = createPdoConnection();
} else {
    $mysqli = createMysqliConnection(); // Créez une connexion MySQLi
}

// Variables
$email = "";
$name = "";
$errors = array();

// Si l'utilisateur clique sur le bouton d'inscription
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Validation des entrées
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
    $email_check = "SELECT * FROM usertable WHERE email = ?";
    $stmt = $mysqli->prepare($email_check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors['email'] = "L'email que vous avez saisi existe déjà !";
    }

    // Si pas d'erreurs, insérer l'utilisateur
    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111); // Générer le code de vérification
        $status = "notverified";

        // Insérer les données dans la base
        $insert_data = "INSERT INTO usertable (name, email, password, code, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_data);
        $stmt->bind_param("sssss", $name, $email, $encpass, $code, $status);
        $data_check = $stmt->execute();

        if ($data_check) {
            // Afficher le code de vérification
            $info = "Votre code de vérification est : $code";
            $_SESSION['info'] = $info;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            header('location: user-otp.php');
            exit();
        } else {
            $errors['db-error'] = "Échec lors de l'insertion de données dans la base de données !";
        }
    }
}

// Si l'utilisateur clique sur le bouton de soumission du code de vérification
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code = "SELECT * FROM usertable WHERE code = ?";
    $stmt = $mysqli->prepare($check_code);
    $stmt->bind_param("s", $otp_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $fetch_data = $result->fetch_assoc();
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'verified';

        // Mettre à jour le code et le statut
        $update_otp = "UPDATE usertable SET code = ?, status = ? WHERE code = ?";
        $stmt = $mysqli->prepare($update_otp);
        $stmt->bind_param("ssi", $code, $status, $fetch_code);
        $update_res = $stmt->execute();

        if ($update_res) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            header('location: /index.php');
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
    $check_email = "SELECT * FROM usertable WHERE email = ?";
    $stmt = $mysqli->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetch = $result->fetch_assoc();
        $fetch_pass = $fetch['password'];
        if (password_verify($password, $fetch_pass)) {
            $_SESSION['email'] = $email;
            $status = $fetch['status'];
            if ($status == 'verified') {
                $_SESSION['password'] = $password;
                header('location: /index.php');
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
    $check_email = "SELECT * FROM usertable WHERE email = ?";
    $stmt = $mysqli->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $code = rand(999999, 111111);
        $insert_code = "UPDATE usertable SET code = ? WHERE email = ?";
        $stmt = $mysqli->prepare($insert_code);
        $stmt->bind_param("is", $code, $email);
        $run_query = $stmt->execute();

        if ($run_query) {
            // Afficher le code au lieu de l'envoyer par email
            $info = "Votre code de réinitialisation du mot de passe est : $code";
            $_SESSION['info'] = $info;
            $_SESSION['email'] = $email;
            header('location: reset-code.php');
            exit();
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
    $check_code = "SELECT * FROM usertable WHERE code = ?";
    $stmt = $mysqli->prepare($check_code);
    $stmt->bind_param("s", $otp_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetch_data = $result->fetch_assoc();
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
        $update_pass = "UPDATE usertable SET code = ?, password = ? WHERE email = ?";
        $stmt = $mysqli->prepare($update_pass);
        $stmt->bind_param("iss", $code, $encpass, $email);
        $run_query = $stmt->execute();
        
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