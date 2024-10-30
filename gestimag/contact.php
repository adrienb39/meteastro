<?php

require "connexion_bdd.php"; // Assurez-vous que ce fichier contient la fonction createMysqliConnection()

$dbType = 'pdo';

if ($dbType === 'pdo') {
    $db = createPdoConnection();
} else {
    $mysqli = createMysqliConnection(); // Créez une connexion MySQLi
}

function cleanInput($data) {
    $data = trim($data); // Supprime les espaces en début et fin
    $data = stripslashes($data); // Supprime les antislashs
    $data = htmlspecialchars($data); // Convertit les caractères spéciaux en entités HTML
    return $data;
}

// Vérification de la méthode de requête
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialisation des variables
    $name = $email = $subject = $message = "";
    $errors = [];

    // Validation des champs
    if (empty($_POST["name"])) {
        $errors[] = "Le nom est requis.";
    } else {
        $name = cleanInput($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email = cleanInput($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }

    if (empty($_POST["subject"])) {
        $errors[] = "Le sujet est requis.";
    } else {
        $subject = cleanInput($_POST["subject"]);
    }

    if (empty($_POST["message"])) {
        $errors[] = "Le message est requis.";
    } else {
        $message = cleanInput($_POST["message"]);
    }

    // Si des erreurs existent, les afficher
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        // Connexion à la base de données
        $pdo = createPdoConnection();

        // Préparation et exécution de la requête
        $stmt = $pdo->prepare('INSERT INTO contact_gestimag (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $subject, $message]);

        echo "<p style='color: green;'>Votre message a été envoyé avec succès.</p>";
    }
} else {
    echo "<p style='color: red;'>Méthode de requête non valide.</p>";
}