<?php
require_once __DIR__ . "/../config/connexion_bdd.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if (isset($_GET['id']) && isset($_GET['table'])) {
	$id_to_verify = (int) $_GET['id'];
	$table_to_verify = $_GET['table'];
	$allowed = ['astronomie', 'meteorologie'];

	if (in_array($table_to_verify, $allowed)) {
		try {
			$update = $db->prepare("UPDATE $table_to_verify SET verified = 'y' WHERE id = :id");
			$update->execute([':id' => $id_to_verify]);
			$message = "✅ Contenu validé avec succès !";
		} catch (PDOException $e) {
			$message = "❌ Erreur de validation : " . $e->getMessage();
		}
	}
}

/**
 * Récupère la liste des catégories uniques
 */
function getCategories($db, $table)
{
	try {
		$stmt = $db->query("SELECT DISTINCT title FROM $table WHERE title IS NOT NULL AND title != '' ORDER BY title ASC");
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	} catch (PDOException $e) {
		return [];
	}
}

function envoyerMailValidation($table, $data)
{
	$mail = new PHPMailer(true);

	try {
		// --- Configuration Serveur ---
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'dvmta39@gmail.com';
		$mail->Password = 'pnnikshkztituxfj';
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Port = 587;
		$mail->CharSet = 'UTF-8';

		// --- Destinataires ---
		$mail->setFrom('dvmta39@gmail.com', 'Système de Modération');
		$mail->addAddress('dvmta39@gmail.com');

		// --- Pièces Jointes (Images et Musique) ---
		$uploadDir = "uploads/";

		// Image principale
		if (!empty($data['filename']) && file_exists($uploadDir . $data['filename'])) {
			$mail->addAttachment($uploadDir . $data['filename'], 'Image_Principale');
		}

		// Image de fond
		if (!empty($data['background_img']) && file_exists($uploadDir . $data['background_img'])) {
			$mail->addAttachment($uploadDir . $data['background_img'], 'Image_Fond');
		}

		// Fichier Musique
		if (!empty($data['music_file']) && file_exists($uploadDir . $data['music_file'])) {
			$mail->addAttachment($uploadDir . $data['music_file'], 'Musique_Ambiance');
		}

		// --- Contenu du mail ---
		$mail->isHTML(true);
		$mail->Subject = "🔍 Modération Meteastro : " . $data['title_c'];

		$mail->Body = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #333; padding: 20px; border-radius: 10px; background-color: #f9f9f9;'>
                <h2 style='color: #1d4ed8;'>Nouveau contenu à vérifier</h2>
                <p><strong>Section :</strong> " . ucfirst($table) . "</p>
                <p><strong>Catégorie :</strong> " . $data('title') . "</p>
                <p><strong>Titre :</strong> " . $data['title_c'] . "</p>
                <hr>
                <p><strong>Fichiers joints :</strong> " .
			(!empty($data['filename']) ? "Image ✅ " : "") .
			(!empty($data['music_file']) ? "Musique ✅" : "") .
			"</p>
                <hr>
                <p><strong>Aperçu du contenu :</strong></p>
                <div style='background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>
                    " . $data['contenu'] . "
                </div>
                <br>
                <div style='text-align: center; margin-top: 20px;'>
                    <a href='https://meteastro.fr/redirect.php?id=" . $data['id'] . "&table=" . $table . "' 
                       style='background-color: #22c55e; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                       ✅ VÉRIFIER ET APPROUVER
                    </a>
                </div>
                <p style='font-size: 11px; color: #666; margin-top: 20px;'>
                    Note : Les images et musiques sont disponibles en pièces jointes de ce mail.
                </p>
            </div>";

		$mail->send();
		return true;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Traite l'insertion avec gestion des images (couverture + galerie) et musiques
 */
function traiterInsertion($db, $table)
{
	if (!isset($_FILES['uploadfile']) || $_FILES['uploadfile']['error'] !== UPLOAD_ERR_OK) {
		return "Erreur lors du téléchargement de l'image de couverture.";
	}

	$title = !empty($_POST['new_category']) ? trim($_POST['new_category']) : ($_POST['title_select'] ?? '');
	$title = htmlspecialchars($title);

	if (empty($title)) {
		return "Veuillez sélectionner ou créer une catégorie.";
	}

	$title_contenu = htmlspecialchars($_POST['title_contenu']);
	$contenu = $_POST['contenu'];
	$verified = 'n';
	$id_users = $_SESSION['user_id'] ?? 0;
	$uploadDir = __DIR__ . "/../uploads/";

	// --- 1. Image Couverture (Unique) ---
	$filename = time() . "_" . basename($_FILES["uploadfile"]["name"]);
	move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $uploadDir . $filename);

	// --- 2. Image de Fond (Unique) ---
	$background_img = null;
	if (isset($_FILES['background_img']) && $_FILES['background_img']['error'] === UPLOAD_ERR_OK) {
		$background_img = "bg_" . time() . "_" . basename($_FILES["background_img"]["name"]);
		move_uploaded_file($_FILES["background_img"]["tmp_name"], $uploadDir . $background_img);
	}

	// --- 3. Galerie d'images (Multiples) ---
	$gallery_paths = [];
	if (isset($_FILES['gallery_files']) && !empty($_FILES['gallery_files']['name'][0])) {
		foreach ($_FILES['gallery_files']['name'] as $key => $val) {
			if ($_FILES['gallery_files']['error'][$key] === UPLOAD_ERR_OK) {
				$gal_name = "gal_" . time() . "_" . uniqid() . "_" . basename($_FILES["gallery_files"]["name"][$key]);
				if (move_uploaded_file($_FILES["gallery_files"]["tmp_name"][$key], $uploadDir . $gal_name)) {
					$gallery_paths[] = $gal_name;
				}
			}
		}
	}
	$gallery_string = !empty($gallery_paths) ? implode(',', $gallery_paths) : null;

	// --- 4. Musiques (Multiples) ---
	$music_paths = [];
	if (isset($_FILES['music_files']) && !empty($_FILES['music_files']['name'][0])) {
		foreach ($_FILES['music_files']['name'] as $key => $val) {
			if ($_FILES['music_files']['error'][$key] === UPLOAD_ERR_OK) {
				$music_name = "music_" . time() . "_" . uniqid() . "_" . basename($_FILES["music_files"]["name"][$key]);
				if (move_uploaded_file($_FILES["music_files"]["tmp_name"][$key], $uploadDir . $music_name)) {
					$music_paths[] = $music_name;
				}
			}
		}
	}
	$music_string = !empty($music_paths) ? implode(',', $music_paths) : null;

	try {
		// Ajout de gallery_images dans la requête SQL
		$sql = "INSERT INTO $table (title, title_contenu, contenu, filename, background_img, music_file, gallery_images, verified, id_users) 
                VALUES (:title, :title_c, :contenu, :filename, :bg, :music, :gallery, :verified, :id_u)";

		$stmt = $db->prepare($sql);
		$stmt->execute([
			':title' => $title,
			':title_c' => $title_contenu,
			':contenu' => $contenu,
			':filename' => $filename,
			':bg' => $background_img,
			':music' => $music_string,
			':gallery' => $gallery_string,
			':verified' => $verified,
			':id_u' => $id_users
		]);

		$lastId = $db->lastInsertId();
		envoyerMailValidation($table, [
			'id' => $lastId,
			'title' => $title,
			'title_c' => $title_contenu,
			'contenu' => $contenu,
			'filename' => $filename,
			'background_img' => $background_img,
			'music_file' => $music_string
		]);

		header("Location: index.php?msg=Publication réussie !");
		exit();
	} catch (PDOException $e) {
		return "Erreur BDD : " . $e->getMessage();
	}
}

// Initialisation
$catsAstro = getCategories($db, 'astronomie');
$catsMeteo = getCategories($db, 'meteorologie');

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
				<div class="alert alert-danger"
					style="color: #ff4d4d; margin-bottom: 15px; text-align: center; font-weight: bold;">
					<i class="fas fa-exclamation-triangle"></i> <?= $message ?>
				</div>
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
					<div class="image-upload-grid">
						<div class="input-group-custom">
							<label class="label-glass">Image de couverture (Principale)</label>
							<input type="file" name="uploadfile" class="input-glass" required>
						</div>
						<div class="input-group-custom">
							<label class="label-glass">Image de fond (PNG/GIF)</label>
							<input type="file" name="background_img" class="input-glass">
						</div>
					</div>

					<div class="input-group-custom">
						<label class="label-glass">Galerie photos (Ajouter plusieurs images)</label>
						<input type="file" name="gallery_files[]" class="input-glass" accept="image/*" multiple>
					</div>

					<div class="input-group-custom">
						<label class="label-glass">Ajouter une ou plusieurs musiques (MP3/WAV)</label>
						<input type="file" name="music_files[]" class="input-glass" accept="audio/*" multiple>
					</div>

					<div class="category-block">
						<label class="label-glass">Catégorie</label>
						<div class="dual-input">
							<select name="title_select" class="input-glass">
								<option value="">-- Choisir une catégorie --</option>
								<?php foreach ($catsAstro as $cat): ?>
									<option value="<?= $cat ?>"><?= $cat ?></option>
								<?php endforeach; ?>
							</select>
							<span class="or-separator">OU</span>
							<input type="text" name="new_category" placeholder="Créer une nouvelle catégorie"
								class="input-glass">
						</div>
					</div>

					<input type="text" name="title_contenu" placeholder="Titre de l'article" class="input-glass"
						required>

					<div class="editor-container">
						<textarea id="contenu-astronomie" name="contenu" class="summernote"></textarea>
					</div>

					<button type="button" id="btn-submit-astro" class="btn-progress-full btn-submit-astro">
						<div class="progress-fill"></div>
						<span class="btn-text"><i class="fas fa-paper-plane me-2"></i>Lancer la publication</span>
					</button>
				</form>
			</div>

			<div class="form-section form-meteo">
				<h3 class="modal-title">☁️ Nouveau Rapport Climatique</h3>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="image-upload-grid">
						<div class="input-group-custom">
							<label class="label-glass">Image de couverture (Principale)</label>
							<input type="file" name="uploadfile" class="input-glass" required>
						</div>
						<div class="input-group-custom">
							<label class="label-glass">Image de fond (PNG/GIF)</label>
							<input type="file" name="background_img" class="input-glass">
						</div>
					</div>

					<div class="input-group-custom">
						<label class="label-glass">Galerie photos (Ajouter plusieurs images)</label>
						<input type="file" name="gallery_files[]" class="input-glass" accept="image/*" multiple>
					</div>

					<div class="input-group-custom">
						<label class="label-glass">Ajouter une ou plusieurs musiques (MP3/WAV)</label>
						<input type="file" name="music_files[]" class="input-glass" accept="audio/*" multiple>
					</div>

					<div class="category-block">
						<label class="label-glass">Phénomène / Catégorie</label>
						<div class="dual-input">
							<select name="title_select" class="input-glass">
								<option value="">-- Choisir un phénomène --</option>
								<?php foreach ($catsMeteo as $cat): ?>
									<option value="<?= $cat ?>"><?= $cat ?></option>
								<?php endforeach; ?>
							</select>
							<span class="or-separator">OU</span>
							<input type="text" name="new_category" placeholder="Nouveau phénomène" class="input-glass">
						</div>
					</div>

					<input type="text" name="title_contenu" placeholder="Titre de l'article" class="input-glass"
						required>

					<div class="editor-container">
						<textarea id="contenu-meteorologie" name="contenu" class="summernote"></textarea>
					</div>

					<button type="button" id="btn-submit-meteo" class="btn-progress-full btn-submit-meteo">
						<div class="progress-fill"></div>
						<span class="btn-text"><i class="fas fa-cloud-upload-alt me-2"></i>Envoyer le rapport</span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
	/* Styles conservés de votre version originale */
	.image-upload-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 15px;
	}

	.modal-overlay {
		position: fixed;
		inset: 0;
		background: rgba(2, 6, 23, 0.85);
		backdrop-filter: blur(12px);
		z-index: 9999;
		display: none;
		align-items: center;
		justify-content: center;
		padding: 20px;
		opacity: 0;
		transition: opacity 0.3s ease;
	}

	.modal-overlay.active {
		display: flex;
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

	.glass-modal {
		background: rgba(15, 23, 42, 0.8);
		border: 1px solid rgba(255, 255, 255, 0.1);
		border-radius: 28px;
		padding: 40px;
		box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
		max-height: 90vh;
		overflow-y: auto;
	}

	.category-block {
		margin-bottom: 20px;
	}

	.dual-input {
		display: flex;
		flex-direction: column;
		gap: 10px;
		background: rgba(255, 255, 255, 0.03);
		padding: 15px;
		border-radius: 15px;
		border: 1px dashed rgba(255, 255, 255, 0.2);
	}

	.or-separator {
		text-align: center;
		font-size: 0.7rem;
		color: #94a3b8;
		font-weight: bold;
		letter-spacing: 1px;
	}

	select.input-glass {
		cursor: pointer;
	}

	select.input-glass option {
		background: #0f172a;
		color: white;
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
		cursor: pointer;
	}

	.btn-submit-astro {
		background: linear-gradient(135deg, #3b82f6, #1d4ed8);
	}

	.btn-submit-meteo {
		background: linear-gradient(135deg, #ef4444, #b91c1c);
	}

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

	/* Style de base commun aux deux boutons */
	.btn-progress-full {
		position: relative;
		width: 100%;
		padding: 16px;
		border-radius: 14px;
		border: none;
		color: white;
		font-weight: 700;
		margin-top: 20px;
		cursor: pointer;
		overflow: hidden;
		/* Important : pour cacher le remplissage qui dépasse */
		z-index: 1;
		transition: transform 0.2s;
	}

	/* Le calque qui va progresser */
	.progress-fill {
		position: absolute;
		top: 0;
		left: 0;
		height: 100%;
		width: 0%;
		/* Départ à 0% */
		background: rgba(255, 255, 255, 0.2);
		/* Couleur de remplissage (semi-transparente) */
		z-index: -1;
		/* Derrière le texte */
		transition: width 0.3s ease-out;
	}

	/* Couleurs spécifiques quand on ne progresse pas encore */
	.btn-submit-astro {
		background: #1d4ed8;
	}

	.btn-submit-meteo {
		background: #b91c1c;
	}

	/* Couleur du remplissage pendant l'upload (ex: un vert brillant) */
	.btn-progress-full.uploading .progress-fill {
		background: #22c55e;
	}

	/* Pour que le texte reste lisible par-dessus */
	.btn-text {
		position: relative;
		z-index: 2;
	}
</style>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const setupUpload = (btnId, actionName) => {
			const btn = document.getElementById(btnId);
			const form = btn.closest('form');
			const fill = btn.querySelector('.progress-fill');
			const text = btn.querySelector('.btn-text');

			btn.addEventListener('click', function () {
				const formData = new FormData(form);
				formData.append(actionName, 'true');

				const xhr = new XMLHttpRequest();

				// Démarrage de l'upload
				xhr.upload.addEventListener("progress", function (e) {
					if (e.lengthComputable) {
						btn.classList.add('uploading');
						btn.style.pointerEvents = "none"; // Désactive le clic

						const percent = Math.round((e.loaded / e.total) * 100);
						fill.style.width = percent + "%";
						text.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${percent}%`;
					}
				});

				xhr.onload = function () {
					if (xhr.status === 200) {
						text.innerHTML = "✅ Terminé !";
						window.location.href = "redirect.php";
					} else {
						alert("Erreur !");
						btn.classList.remove('uploading');
						btn.style.pointerEvents = "auto";
					}
				};

				xhr.open("POST", "");
				xhr.send(formData);
			});
		};

		setupUpload('btn-submit-astro', 'insert-astronomie');
		setupUpload('btn-submit-meteo', 'insert-meteorologie');
	});
</script>