<?php
require_once __DIR__ . "/../../config/connexion_bdd.php";

// Créer la connexion PDO
$pdo = createPdoConnection();

// Récupérer la clé de licence envoyée
$licenseKey = $_POST['license_key'];

// Vérifier si cette clé de licence existe déjà dans la base de données pour un autre utilisateur
$stmt = $pdo->prepare("SELECT COUNT(*) FROM licenses WHERE license_key = ?");
$stmt->execute([$licenseKey]);
$isValid = $stmt->fetchColumn() == 0;  // Si 0, cela signifie qu'elle n'existe pas déjà

// Retourner le résultat sous forme de JSON
echo json_encode(['isValid' => $isValid]);