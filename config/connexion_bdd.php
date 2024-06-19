<?php
$servername = "localhost";
$username = "root";
$password = "Robot500";
$dbname = "meteastro";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);
// Vérifier la connexion
if ($conn->connect_error) {
     die("Connexion a échouée: " . $conn->connect_error);
}
?>