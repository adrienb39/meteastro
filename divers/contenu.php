<?php require_once "config/controllerUserData.php"; ?>
<?php
$email = $_SESSION['email'];
$password = $_SESSION['password'];
if ($email != false && $password != false) {
	$sql = "SELECT * FROM usertable WHERE email = '$email'";
	$run_Sql = mysqli_query($conn, $sql);
	if ($run_Sql) {
		$fetch_info = mysqli_fetch_assoc($run_Sql);
		$status = $fetch_info['status'];
		$code = $fetch_info['code'];
		if ($status == "verified") {
			if ($code != 0) {
				header('Location: connexion/reset-code.php');
			}
		} else {
			header('Location: connexion/user-otp.php');
		}
	}
} else {
	header('Location: connexion/login-user.php');
}
?>
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
<?php
session_start();
error_reporting(0);

// Si le bouton d'insertion de données météorologiques est cliqué
if (isset($_POST['insert-meteorologie'])) {
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

	// Requête SQL pour insérer des données dans la table meteorologie, y compris le nom de l'image
	$sqlMeteorologie = "INSERT INTO meteorologie (title, title_contenu, contenu, filename, verified, id_users) VALUES ('$title', '$title_contenu', '$contenu', '$filename', '$verified', '$id_users')";
	$result = mysqli_query($conn, $sqlMeteorologie);
	if ($result) {
		header("Location: ../index-connect.php?msg=Donnée enregistrée avec succes");
	} else {

	}
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<style>
		.box {
			border: 1px solid #c4c4c4;
			padding: 30px 25px 10px 25px;
			background: white;
			margin: 50px auto;
			width: 360px;
			text-align: center;
		}

		.logo {
			width: 180px;
			margin-bottom: -10px;
			margin-top: -60px;
		}

		h1.box-logo a {
			text-decoration: none;
		}

		h1.box-title {
			color: #AEAEAE;
			background: #f8f8f8;
			font-weight: 300;
			padding: 15px 25px;
			line-height: 30px;
			font-size: 25px;
			text-align: center;
			margin: -27px -26px 26px;
		}

		.box-button {
			border-radius: 5px;
			background: #d2483c;
			text-align: center;
			cursor: pointer;
			font-size: 19px;
			width: 100%;
			height: 51px;
			padding: 0;
			color: #fff;
			border: 0;
			outline: 0;
		}

		.box-register {
			text-align: center;
			margin-bottom: 0px;
		}

		.box-register a {
			text-decoration: none;
			font-size: 12px;
			color: #666;
		}

		.box-input {
			font-size: 14px;
			background: #fff;
			border: 1px solid #ddd;
			margin-bottom: 25px;
			padding-left: 10px;
			border-radius: 5px;
			width: 347px;
			height: 50px;
		}

		.box-input:focus {
			outline: none;
			border-color: #5c7186;
		}

		.sucess {
			text-align: center;
			color: white;
		}

		.sucess a {
			text-decoration: none;
			color: #58aef7;
		}

		p.errorMessage {
			background-color: #e66262;
			border: #AA4502 1px solid;
			padding: 5px 10px;
			color: #FFFFFF;
			border-radius: 3px;
		}

		/* popup */
		/* #popup_open {
			margin: 0;
			cursor: pointer;
			text-align: center;
			color: black;
			font-weight: bold;
			background: none;
			border: none;
			font-size: 16px;
		} */

		/* #popup_open:hover {
			color: red;
		} */

		#popup_box form .submit-contenu {
			margin: 10px 0;
			padding: 4px 20px;
			width: 70%;
			border: 2px solid grey;
			border-radius: 12px;
		}

		#popup_box form input[type='submit'] {
			background: red;
		}

		#popup_box form input[type='submit']:hover {
			cursor: pointer;
			color: #fff;
			font-weight: bold;
			background: #E75757;
		}

		#popup_overlay {
			position: fixed;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			z-index: 9999;
			background: black;
			opacity: 0.7;
			cursor: pointer;
		}

		#popup_box {
			position: fixed;
			top: -2000px;
			left: 25%;
			right: 25%;
			background-color: #fff;
			padding: 0;
			border: 2px solid #ccc;
			border-top: 0px solid #ccc;
			border-radius: 0 0 20px 20px;
			box-shadow: 0 1px 5px #333;
			z-index: 99999;
			max-width: 761px;
			margin: 0 auto;
			max-height: 90%;
			overflow-y: auto;
			overflow-x: hidden;
		}

		#popup_close {
			position: absolute;
			width: 24px;
			height: 24px;
			top: 10px;
			right: 10px;
			cursor: pointer;
			border-radius: 50%;
			background: #fff;
			text-align: center;
			line-height: 22px;
		}

		#popup_box h1 {
			color: #000;
			margin: 0;
			padding: 10px 20px;
			text-align: center;
			background: #E75757;
			border: 1px solid transparent;
			border-radius: 12px;
		}

		#popup_box form {
			padding: 10px 15%;
			display: flex;
			justify-content: center;
			flex-direction: column;
			align-items: center;
		}

		#popup_box form label {
			display: inline-block;
			text-align: right;
			padding-right: 10px;
			width: 25%;
		}

		#popup_box form input {
			display: inline-block;
		}

		input.ajout-contenu {
			padding: 4px 20px;
			margin: 10px 10%;
			width: 100%;
			border: 2px solid gray;
			border-radius: 10px;
		}

		textarea.ajout-contenu-text {
			padding: 4px 20px;
			margin: 10px 10%;
			width: 100%;
			height: 10rem;
			border: 2px solid gray;
			border-radius: 10px;
		}

		/* phone */
		@media screen and (max-width: 640px) {
			#popup_box {
				width: 98%;
				left: 1%;
				right: 1%;
			}
		}

		.login {
			max-width: 700px;
			width: 100%;
			margin: 40px auto;
		}

		.login nav {
			position: relative;
			width: 100%;
			height: 50px;
			display: flex;
			align-items: center;
		}

		.login nav label {
			display: block;
			height: 100%;
			width: 100%;
			text-align: center;
			line-height: 50px;
			cursor: pointer;
			position: relative;
			z-index: 1;
			color: red;
			font-size: 17px;
			border-radius: 12px;
			margin: 0 5px;
			transition: all 0.3s ease;
		}

		.login nav label:hover {
			background: red;
			color: white;
		}

		#astronomie2:checked~nav label.astronomie2,
		#meteorologie2:checked~nav label.meteorologie2 {
			color: #fff;
		}

		nav label i {
			padding-right: 7px;
		}

		nav .slider {
			position: absolute;
			height: 100%;
			width: 50%;
			left: 0;
			bottom: 0;
			z-index: 0;
			border-radius: 12px;
			background: red;
			transition: all 0.3s ease;
		}

		input[type="radio"] {
			display: none;
		}

		#meteorologie2:checked~nav .slider {
			left: 50%;
		}

		section .content-login {
			display: none;
			background: #fff;
		}

		#astronomie2:checked~section .content-login-1,
		#meteorologie2:checked~section .content-login-2 {
			display: block;
		}

		/* responsive */
		@media screen and (max-width: 800px) {
			.login {
				margin-left: -4px;
				padding: 4px;
			}
		}
	</style>
</head>

<body>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<button class="btn btn-close-white btn-outline-danger fs-5 mb-5" id="popup_open">ACCÈS À L'AJOUT DU CONTENU</button>
	<div id="popup_overlay" style="display: none;"></div>
	<div id="popup_box" style="top: -2000px;">
		<a id="popup_close">X</a>
		<div class="login">
			<input type="radio" name="slider-login" checked id="astronomie2">
			<input type="radio" name="slider-login" id="meteorologie2">
			<nav>
				<label for="astronomie2" class="astronomie2">ASTRONOMIE</label>
				<label for="meteorologie2" class="meteorologie2">METEOROLOGIE</label>
				<div class="slider"></div>
			</nav>
			<section>
				<div class="content-login content-login-1">
					<h1>Ajout du contenu pour l'Astronomie</h1>
					<div class="container">
						<div class="form-group">
							<form action="" method="post" enctype="multipart/form-data">
								<input class="ajout-contenu" type="file" name="uploadfile" value="" />
								<input type="text" class="ajout-contenu" name="title" placeholder="Titre">
								<input type="text" class="ajout-contenu" name="title_contenu"
									placeholder="Titre du contenu">
								<textarea class="form-control" id="contenu-astronomie" name="contenu"
									style="height: 300px;"></textarea>
								<input style="display: none;" type="text" class="ajout-contenu" name="verified"
									value="n">
								<?php $users = $_SESSION['username']; ?>
								<input style="display: none;" type="text" name="users"
									value="<?php echo $_SESSION['username']; ?>" />
								<input type="submit" class="submit-contenu" name="insert-astronomie" value="Insérer">
							</form>
						</div>
					</div>
				</div>
				<div class="content-login content-login-2">
					<h1>Ajout du contenu pour la Meteorologie</h1>
					<div class="container">
						<div class="form-group">
							<form action="" method="post">
								<input type="text" class="ajout-contenu" name="title" placeholder="Titre">
								<input type="text" class="ajout-contenu" name="title_contenu"
									placeholder="Titre du contenu">
								<textarea class="form-control" id="contenu-meteorologie" name="contenu"
									style="height: 300px;"></textarea>
								<input style="display: none;" type="text" class="ajout-contenu" name="title_contenu"
									value="n">
								<?php $users = $_SESSION['username']; ?>
								<input style="display: none;" type="text" name="users"
									value="<?php echo $_SESSION['username']; ?>" />
								<input type="submit" class="submit-contenu" name="insert-meteorologie" value="Insérer">
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
	<script src="divers/popup.js"></script>
</body>

</html>