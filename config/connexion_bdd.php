<?php
session_start();
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);
// Vérifier la connexion
if ($conn->connect_error) {
     die("Connexion a échouée: " . $conn->connect_error);
}
?>