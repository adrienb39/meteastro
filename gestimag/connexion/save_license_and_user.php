<?php
require_once __DIR__ . "/User.php";
session_start();
require __DIR__ . "/../../config/connexion_bdd.php";

// Créer la connexion PDO
$pdo = createPdoConnection();

// Récupérer les données envoyées par le formulaire AJAX
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];
$consent = $_POST['consent'];
$software_id = $_POST['software_id'];
$license_key = $_POST['license_key'];

// Hashage du mot de passe
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insérer l'utilisateur dans la base de données
$stmt = $pdo->prepare("INSERT INTO users_gestimag (name, email, password, consent, software_id) VALUES (?, ?, ?, ?, ?)");
if ($stmt->execute([$name, $email, $hashedPassword, $consent, $software_id])) {
    // Récupérer l'ID de l'utilisateur
    $userId = $pdo->lastInsertId();

    // Insérer la licence dans la table `licenses`
    $stmt = $pdo->prepare("INSERT INTO licenses (id_software, id_users_gestimag, license_key, created_at) VALUES (?, ?, ?, NOW())");
    if ($stmt->execute([$software_id, $userId, $license_key])) {
    } 
}