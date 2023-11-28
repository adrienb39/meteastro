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
		#popup_open {
			margin: 0;
			cursor: pointer;
			text-align: center;
			color: black;
			font-weight: bold;
			background: none;
			border: none;
			font-size: 16px;
		}

		#popup_open:hover {
			color: red;
		}

		#popup_box form input {
			margin: 10px 0;
			padding: 0 20px;
			border: 2px solid #ccc;
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
		}

		#popup_box form {
			padding: 10px 25px;
		}

		#popup_box form label {
			display: inline-block;
			text-align: right;
			padding-right: 10px;
			width: 25%;
		}

		#popup_box form input {
			display: inline-block;
			width: 70%;
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
			border-radius: 5px;
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
			border-radius: 5px;
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
	<button id="popup_open">ACCÈS À L'AJOUT DU CONTENU</button>
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
					<form action="/divers/contenu-astronomie.php" method="post">
						<input style="padding: 4px 20px; margin: 10px 10%;" type="text" name="title" placeholder="Titre">
						<input style="padding: 4px 20px; margin: 10px 10%;" type="text" name="title_contenu" placeholder="Titre du contenu">
						<textarea style="padding: 4px 2px; margin: 10px 10%;" name="contenu" cols="68" rows="10" placeholder="Contenu"></textarea>
						<?php $users = $_SESSION['username']; ?>
						<input style="display: none;" type="text" name="users"
							value="<?php echo $_SESSION['username']; ?>" />
						<input style="padding: 4px 20px; margin: 10px 13%;" type="submit" name="insert" value="Insérer">
					</form>
				</div>
				<div class="content-login content-login-2">
					<h1>Ajout du contenu pour la Meteorologie</h1>
					<form action="/divers/contenu-meteorologie.php" method="post">
						<input style="padding: 4px 20px; margin: 10px 10%;" type="text" name="title" placeholder="Titre">
						<input style="padding: 4px 20px; margin: 10px 10%;" type="text" name="title_contenu" placeholder="Titre du contenu">
						<textarea style="padding: 4px 2px; margin: 10px 10%;" name="contenu" cols="68" rows="10" placeholder="Contenu"></textarea>
						<?php $users = $_SESSION['username']; ?>
						<input style="display: none;" type="text" name="users"
							value="<?php echo $_SESSION['username']; ?>" />
						<input style="padding: 4px 20px; margin: 10px 13%;" type="submit" name="insert" value="Insérer">
					</form>
				</div>
			</section>
		</div>
	</div>
	<script src="divers/popup.js"></script>
</body>

</html>