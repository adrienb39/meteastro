<?php
include ("config/connexion_bdd.php");
$pseudo = htmlentities(trim($_POST["pseudo"]));
$nom = htmlentities(trim($_POST["nom"]));
$prenom = htmlentities(trim($_POST["prenom"]));
$email = htmlentities(trim($_POST["email"]));
$message = htmlentities(trim($_POST["message"]));
$reg = "SELECT * FROM contact WHERE pseudo ='$pseudo'";
$sql = "INSERT INTO contact (pseudo, nom, prenom, email, message)
VALUES('$pseudo','$nom','$prenom','$email','$message')";
if ($conn->query($sql)) {
    echo "<script>alert('Vos informations ont bien été envoyés');document.location='/'</script>";
} else {
    echo "<script type= 'text/javascript'>alert('Vos informations n'ont pas été envoyés');document.location='/'</script>";
}
?>