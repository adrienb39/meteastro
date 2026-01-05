<?php
// On suppose que la session est déjà démarrée dans le fichier parent
require_once __DIR__ . "/../config/connexion_bdd.php";

$message = "";

/**
 * Fonction unique pour traiter l'insertion de contenu
 */
function traiterInsertion($db, $table)
{
	if (!isset($_FILES['uploadfile']) || $_FILES['uploadfile']['error'] !== UPLOAD_ERR_OK) {
		return "Erreur lors du téléchargement de l'image.";
	}

	$title = htmlspecialchars($_POST['title']);
	$title_contenu = htmlspecialchars($_POST['title_contenu']);
	$contenu = $_POST['contenu'];
	$verified = 'n';
	$id_users = $_SESSION['user_id'] ?? 0;

	$filename = time() . "_" . basename($_FILES["uploadfile"]["name"]);
	$folder = __DIR__ . "/../uploads/" . $filename;

	if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $folder)) {
		try {
			$sql = "INSERT INTO $table (title, title_contenu, contenu, filename, verified, id_users) 
                    VALUES (:title, :title_c, :contenu, :filename, :verified, :id_u)";

			$stmt = $db->prepare($sql);
			$stmt->execute([
				':title' => $title,
				':title_c' => $title_contenu,
				':contenu' => $contenu,
				':filename' => $filename,
				':verified' => $verified,
				':id_u' => $id_users
			]);

			header("Location: index.php?msg=Succès !");
			exit();
		} catch (PDOException $e) {
			return "Erreur BDD : " . $e->getMessage();
		}
	}
	return "Échec du téléchargement.";
}

if (isset($_POST['insert-astronomie'])) {
	$message = traiterInsertion($db, 'astronomie');
} elseif (isset($_POST['insert-meteorologie'])) {
	$message = traiterInsertion($db, 'meteorologie');
}
?>

<div id="popup_overlay" class="modal-overlay">
	<div id="popup_box" class="modal-content-wrapper">
		<div class="content-card glass-modal">

			<button type="button" class="close-modal-btn" id="popup_close" aria-label="Fermer">
				<i class="fas fa-times"></i>
			</button>

			<?php if ($message): ?>
				<div class="alert alert-danger"><?= $message ?></div>
			<?php endif; ?>

			<input type="radio" name="nav-tab" id="tab-astro" checked hidden>
			<input type="radio" name="nav-tab" id="tab-meteo" hidden>

			<div class="tab-nav">
				<label for="tab-astro"><i class="fas fa-user-astronaut me-2"></i>Astronomie</label>
				<label for="tab-meteo"><i class="fas fa-cloud-sun me-2"></i>Météorologie</label>
				<div class="tab-slider"></div>
			</div>

			<div class="form-section form-astro">
				<h3 class="modal-title">🚀 Nouvelle Publication Stellaire</h3>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="input-group-custom">
						<label class="label-glass">Image de couverture</label>
						<input type="file" name="uploadfile" class="input-glass" required>
					</div>

					<input type="text" name="title" placeholder="Catégorie (ex: Galaxies)" class="input-glass" required>
					<input type="text" name="title_contenu" placeholder="Titre de l'article" class="input-glass"
						required>

					<div class="editor-container">
						<textarea id="contenu-astronomie" name="contenu" class="summernote"></textarea>
					</div>

					<button type="submit" name="insert-astronomie" class="btn-submit-astro">
						<i class="fas fa-paper-plane me-2"></i>Lancer la publication
					</button>
				</form>
			</div>

			<div class="form-section form-meteo">
				<h3 class="modal-title">☁️ Nouveau Rapport Climatique</h3>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="input-group-custom">
						<label class="label-glass">Image de couverture</label>
						<input type="file" name="uploadfile" class="input-glass" required>
					</div>

					<input type="text" name="title" placeholder="Phénomène (ex: Orages)" class="input-glass" required>
					<input type="text" name="title_contenu" placeholder="Titre de l'article" class="input-glass"
						required>

					<div class="editor-container">
						<textarea id="contenu-meteorologie" name="contenu" class="summernote"></textarea>
					</div>

					<button type="submit" name="insert-meteorologie" class="btn-submit-meteo">
						<i class="fas fa-cloud-upload-alt me-2"></i>Envoyer le rapport
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<style>
	/* --- FOND ET POSITIONNEMENT DU MODAL --- */
	.modal-overlay {
		position: fixed;
		inset: 0;
		background: rgba(2, 6, 23, 0.85);
		backdrop-filter: blur(12px);
		z-index: 9999;
		display: none;
		/* Caché par défaut */
		align-items: center;
		justify-content: center;
		padding: 20px;
		opacity: 0;
		/* Ajouté pour la transition */
		transition: opacity 0.3s ease;
	}

	/* Quand le JS ajoute .active */
	.modal-overlay.active {
		opacity: 1;
	}

	.modal-content-wrapper {
		width: 100%;
		max-width: 850px;
		position: relative;
		transform: translateY(30px);
		opacity: 0;
		transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
	}

	.modal-overlay.active .modal-content-wrapper {
		transform: translateY(0);
		opacity: 1;
	}

	/* --- DESIGN DE LA CARTE --- */
	.glass-modal {
		background: rgba(15, 23, 42, 0.8);
		border: 1px solid rgba(255, 255, 255, 0.1);
		border-radius: 28px;
		padding: 40px;
		box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
		max-height: 90vh;
		overflow-y: auto;
	}

	.close-modal-btn {
		position: absolute;
		top: 20px;
		right: 20px;
		background: rgba(255, 255, 255, 0.1);
		border: none;
		color: white;
		width: 40px;
		height: 40px;
		border-radius: 50%;
		cursor: pointer;
		transition: 0.3s;
		z-index: 10;
	}

	.close-modal-btn:hover {
		background: #ef4444;
		transform: rotate(90deg);
	}

	.modal-title {
		color: white;
		font-weight: 700;
		margin-bottom: 25px;
		text-align: center;
	}

	/* --- TABS (ONGLETS) --- */
	.tab-nav {
		display: flex;
		background: rgba(0, 0, 0, 0.4);
		border-radius: 50px;
		padding: 5px;
		margin-bottom: 35px;
		position: relative;
	}

	.tab-nav label {
		flex: 1;
		text-align: center;
		padding: 14px;
		cursor: pointer;
		z-index: 2;
		transition: 0.3s;
		color: rgba(255, 255, 255, 0.6);
		font-weight: 600;
	}

	.tab-slider {
		position: absolute;
		width: calc(50% - 5px);
		height: calc(100% - 10px);
		background: #3b82f6;
		top: 5px;
		left: 5px;
		border-radius: 40px;
		transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
		z-index: 1;
	}

	#tab-meteo:checked~.tab-nav .tab-slider {
		left: 50%;
		background: #ef4444;
	}

	#tab-astro:checked~.tab-nav label[for="tab-astro"],
	#tab-meteo:checked~.tab-nav label[for="tab-meteo"] {
		color: white;
	}

	/* --- ÉLÉMENTS FORMULAIRE --- */
	.input-glass {
		width: 100%;
		background: rgba(255, 255, 255, 0.05);
		border: 1px solid rgba(255, 255, 255, 0.1);
		border-radius: 12px;
		padding: 14px;
		color: white;
		margin-bottom: 20px;
	}

	.input-glass:focus {
		border-color: #3b82f6;
		outline: none;
		background: rgba(255, 255, 255, 0.1);
	}

	.label-glass {
		display: block;
		color: #94a3b8;
		font-size: 0.85rem;
		margin-bottom: 8px;
	}

	.btn-submit-astro,
	.btn-submit-meteo {
		width: 100%;
		padding: 16px;
		border-radius: 14px;
		border: none;
		color: white;
		font-weight: 700;
		margin-top: 20px;
		transition: 0.3s;
	}

	.btn-submit-astro {
		background: linear-gradient(135deg, #3b82f6, #1d4ed8);
	}

	.btn-submit-meteo {
		background: linear-gradient(135deg, #ef4444, #b91c1c);
	}

	.btn-submit-astro:hover,
	.btn-submit-meteo:hover {
		transform: translateY(-3px);
		box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
	}

	/* --- LOGIQUE AFFICHAGE --- */
	.form-section {
		display: none;
		animation: fadeInModal 0.4s ease;
	}

	#tab-astro:checked~.form-astro,
	#tab-meteo:checked~.form-meteo {
		display: block;
	}

	@keyframes fadeInModal {
		from {
			opacity: 0;
			transform: scale(0.98);
		}

		to {
			opacity: 1;
			transform: scale(1);
		}
	}
</style>