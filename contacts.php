<?php
include ("../connexion_bdd.php");
$pseudo= htmlentities(trim($_POST["pseudo"]));
$nom= htmlentities(trim($_POST["nom"]));
$prenom= htmlentities(trim($_POST["prenom"]));
$email= htmlentities(trim($_POST["email"]));
$tel= htmlentities(trim($_POST["tel"]));
$choix= htmlentities(trim($_POST["choix"]));
$message= htmlentities(trim($_POST["message"]));
$reg = "SELECT test(*) FROM essai WHERE pseudo ='$pseudo'";
if($reg!=0){
    echo "Ce pseudo n'est pas disponible";
}
$sql = "INSERT INTO essai (pseudo, nom, prenom, email, tel, choix, message)
VALUES('$pseudo','$nom','$prenom','$email','$tel','$choix','$message')";
if ($db->query($sql)) {
echo "<script type= 'text/javascript'>alert('Vos informations ont bien été envoyés');</script>";
}
else{
echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
}
?>