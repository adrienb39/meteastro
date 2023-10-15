<?php
require('config.php');

if (isset($_REQUEST['username'], $_REQUEST['email'], $_REQUEST['password'])) {
    // récupérer le nom et supprimer les antislashes ajoutés par le formulaire
    $nom = stripslashes($_REQUEST['nom']);
    $nom = mysqli_real_escape_string($conn, $nom);
    // récupérer le prénom et supprimer les antislashes ajoutés par le formulaire
    $prenom = stripslashes($_REQUEST['prenom']);
    $prenom = mysqli_real_escape_string($conn, $prenom);
    // récupérer le nom d'utilisateur et supprimer les antislashes ajoutés par le formulaire
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($conn, $username);
    // récupérer le type et supprimer les antislashes ajoutés par le formulaire
    $type = stripslashes($_REQUEST['type']);
    $type = mysqli_real_escape_string($conn, $type);
    // récupérer l'email et supprimer les antislashes ajoutés par le formulaire
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($conn, $email);
    // récupérer le mot de passe et supprimer les antislashes ajoutés par le formulaire
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($conn, $password);

    $query = "INSERT INTO `users` (nom, prenom, username, type, email, password)
				VALUES ('$nom', '$prenom', '$username', '$type', '$email', '" . hash('sha256', $password) . "')";
    $res = mysqli_query($conn, $query);

    if ($res) {
        echo "<div class='sucess'>
             <h3>Vous êtes inscrit avec succès.</h3>
             <p>Cliquez ici pour vous <a href='test/test.php'>connecter</a></p>
			 </div>";
    }
} else {
    ?>
    <?php
    include "login.php";
?>
<?php } ?>