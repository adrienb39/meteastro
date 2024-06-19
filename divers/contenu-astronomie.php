<?php
session_start();
error_reporting(0);

// Si le bouton d'insertion de données astronomiques est cliqué
if (isset($_POST['insert-astronomie'])) {
	// Récupérer les valeurs du formulaire
	$title = $_POST['title'];
	$title_contenu = $_POST['title_contenu'];
	$contenu = $_POST['contenu'];
	$verified = $_POST['verified'];
	$id_users = $fetch_info['id_users']; // Assurez-vous que cette variable est définie correctement

	// Gestion du téléchargement d'image
	$filename = $_FILES["uploadfile"]["name"];
	$tempname = $_FILES["uploadfile"]["tmp_name"];
	$folder = "./uploads/" . $filename;

	// Déplacer l'image téléchargée dans le dossier : upload
	if (move_uploaded_file($tempname, $folder)) {
		echo "<h3>Image téléchargée avec succès !</h3>";
	} else {
		echo "<h3>Échec du téléchargement de l'image !</h3>";
	}

	// Requête SQL pour insérer des données dans la table astronomie, y compris le nom de l'image
	$sqlAstronomie = "INSERT INTO astronomie (title, title_contenu, contenu, filename, verified, id_users) VALUES ('$title', '$title_contenu', '$contenu', '$filename', '$verified', '$id_users')";
	$result = mysqli_query($conn, $sqlAstronomie);
	if ($result) {
		header("Location: ../index-connect.php?msg=Donnée enregistrée avec succes");
	} else {

	}
}
?>