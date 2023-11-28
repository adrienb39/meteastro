<?php
require('config.php');
session_start();
$_SESSION['name'] = $name;

if (isset($_POST['username'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($conn, $username);
    $_SESSION['username'] = $username;
    $type = stripslashes($_REQUEST['type']);
    $type = mysqli_real_escape_string($conn, $type);
    $_SESSION['type'] = $type;
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($conn, $password);
    $query = "SELECT * FROM `users` WHERE username='$username' AND type='$type' AND password='" . hash('sha256', $password) . "'";
    $result = mysqli_query($conn, $query) or die(mysql_error());

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        // vérifier si l'utilisateur est un administrateur ou un utilisateur
        if ($user['type'] == 'INSCRIT') {
            header('location: /index-connect.php');
        } else {
            
        }
    } else {
        $message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
    }
}
?>