<?php
session_start();

if (isset($_SESSION['id'])) {
	$requser = $bdd->prepare("SELECT * FROM membres WHERE id = ?");
	$requser->execute(array($_SESSION['id']));
	$user = $requser->fetch();
	if (isset($_POST['newpseudo']) and !empty($_POST['newpseudo']) and $_POST['newpseudo'] != $user['pseudo']) {
		$newpseudo = htmlspecialchars($_POST['newpseudo']);
		$insertpseudo = $bdd->prepare("UPDATE membres SET pseudo = ? WHERE id = ?");
		$insertpseudo->execute(array($newpseudo, $_SESSION['id']));
		header('Location: ../index-connect.php?id=' . $_SESSION['id']);
	}
	if (isset($_POST['newmail']) and !empty($_POST['newmail']) and $_POST['newmail'] != $user['mail']) {
		$newmail = htmlspecialchars($_POST['newmail']);
		$insertmail = $bdd->prepare("UPDATE membres SET mail = ? WHERE id = ?");
		$insertmail->execute(array($newmail, $_SESSION['id']));
		header('Location: ../index-connect.php?id=' . $_SESSION['id']);
	}
	if (isset($_POST['newmdp1']) and !empty($_POST['newmdp1']) and isset($_POST['newmdp2']) and !empty($_POST['newmdp2'])) {
		$mdp1 = sha1($_POST['newmdp1']);
		$mdp2 = sha1($_POST['newmdp2']);
		if ($mdp1 == $mdp2) {
			$insertmdp = $bdd->prepare("UPDATE membres SET motdepasse = ? WHERE id = ?");
			$insertmdp->execute(array($mdp1, $_SESSION['id']));
			header('Location: ../index-connect.php?id=' . $_SESSION['id']);
		} else {
			$msg = "Vos deux mdp ne correspondent pas !";
		}
	}
	?>
	<html>

	<head>
		<title>Meteastro : Astronomie / meteorologie</title>
		<meta charset="utf-8">
	</head>

	<body>
		<div align="center">
			<h2>Edition de mon profil</h2>
			<div align="left">
				<form method="POST" action="" enctype="multipart/form-data">
					<label>Pseudo :</label>
					<input type="text" name="newpseudo" placeholder="Pseudo"
						value="<?php echo $user['pseudo']; ?>" /><br /><br />
					<label>Mail :</label>
					<input type="text" name="newmail" placeholder="Mail" value="<?php echo $user['mail']; ?>" /><br /><br />
					<label>Mot de passe :</label>
					<input type="password" name="newmdp1" placeholder="Mot de passe" /><br /><br />
					<label>Confirmation - mot de passe :</label>
					<input type="password" name="newmdp2" placeholder="Confirmation du mot de passe" /><br /><br />
					<input type="submit" value="Mettre à jour mon profil !" />
				</form>
				<?php if (isset($msg)) {
					echo $msg;
				} ?>
			</div>
		</div>
	</body>

	</html>
	<?php
} else {
	header("Location: ../connexion/login.php");
}
?>