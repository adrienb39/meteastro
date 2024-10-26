<?php
session_start();
require "connexion_bdd.php"; // Établir une connexion PDO
$email = "";
$name = "";
$errors = array();

// Inscription
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Validation des données
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'adresse électronique n'est pas valide !";
    }
    if (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères !";
    }
    if ($password !== $cpassword) {
        $errors['password'] = "Les mots de passe ne correspondent pas !";
    }

    // Vérification de l'email
    $stmt = $db->prepare("SELECT * FROM usertable WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        $errors['email'] = "Cet email est déjà enregistré !";
    }

    // Inscription si aucune erreur
    if (empty($errors)) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111); // Générer un code de vérification
        $insert_data = "INSERT INTO usertable (name, email, password, code, status) VALUES (:name, :email, :password, :code, 'notverified')";
        $stmt = $db->prepare($insert_data);
        if ($stmt->execute(['name' => $name, 'email' => $email, 'password' => $encpass, 'code' => $code])) {
            $_SESSION['info'] = "Inscription réussie. Votre code de vérification est : $code"; // Affichage du code
            $_SESSION['email'] = $email;
            header('Location: user-otp.php'); // Rediriger vers la page de vérification
            exit();
        } else {
            $errors['db-error'] = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
}

// Connexion
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM usertable WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
            header('location: /index-connect.php');
            exit();
        } else {
            $errors['login'] = "Courriel ou mot de passe incorrect !";
        }
    } else {
        $errors['login'] = "Aucun utilisateur trouvé avec cet email.";
    }
}

// Changement de mot de passe
if (isset($_POST['change-password'])) {
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password !== $cpassword) {
        $errors['password'] = "Les mots de passe ne correspondent pas !";
    } else {
        $email = $_SESSION['email'];
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE usertable SET password = :password WHERE email = :email");
        if ($stmt->execute(['password' => $encpass, 'email' => $email])) {
            $_SESSION['info'] = "Votre mot de passe a été modifié avec succès.";
            header('Location: password-changed.php');
            exit();
        } else {
            $errors['db-error'] = "Erreur lors du changement de mot de passe. Veuillez réessayer.";
        }
    }
}

// Mot de passe oublié
if (isset($_POST['check-email'])) {
    $email = trim($_POST['email']);
    $stmt = $db->prepare("SELECT * FROM usertable WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $code = rand(999999, 111111); // Générer un code aléatoire
        $_SESSION['reset_code'] = $code; // Stocker le code dans la session
        $_SESSION['email'] = $email; // Stocker l'email dans la session
        $_SESSION['info'] = "Votre code de réinitialisation est : $code"; // Message d'information
        header('Location: reset-code.php'); // Rediriger vers la page de réinitialisation
        exit();
    } else {
        $errors['email'] = "Cette adresse e-mail n'existe pas !";
    }
}

// Vérification du code de réinitialisation
if (isset($_POST['check-reset-otp'])) {
    $otp_code = $_POST['otp'];
    if ($otp_code == $_SESSION['reset_code']) {
        header('Location: new-password.php'); // Rediriger vers la page de création de nouveau mot de passe
        exit();
    } else {
        $errors['otp-error'] = "Le code de réinitialisation est incorrect !";
    }
}

// Affichage des erreurs
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div class='error'>$error</div>";
    }
}

if (isset($_POST['login-now'])) {
    header('Location: login.php');
}