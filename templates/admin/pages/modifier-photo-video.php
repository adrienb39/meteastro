<div class="container">
    <div class="row flex">
        <div class="col py-3">
            <div class="mt-5 mb-5">
                <h2 class="text-center mb-5">
                    <i class="fas fa-edit me-2 text-warning"></i> Modifier le média :
                    <?= htmlspecialchars($media->getTitre()) ?>
                </h2>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white mx-auto" style="max-width: 700px;">
                    <form id="editMediaForm" method="post" action="" enctype="multipart/form-data">

                        <h3 class="card-title mb-4 text-warning fw-bold">
                            <i class="fas fa-info-circle me-2"></i> Informations générales
                        </h3>

                        <div class="form-floating mb-3">
                            <input type="text" name="titre_media" id="titre_media"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                placeholder="Titre du média"
                                value="<?= htmlspecialchars($formData['titre'] ?? $media->getTitre()) ?>" required>
                            <label for="titre_media"><i class="fas fa-tag me-1"></i> Titre du média</label>
                        </div>

                        <div class="mb-4">
                            <label for="type_media" class="form-label fw-semibold text-muted">
                                <i class="fas fa-list me-1"></i> Type de média
                            </label>
                            <select class="form-select <?= isset($error) ? 'border-danger' : '' ?>" id="type_media"
                                name="type_media" required>

                                <?php
                                // Détermine le type de média actuel pour présélectionner l'option
                                $defaultType = ($media->getType() === 'image') ? 'image' :
                                    (filter_var($media->getFichier(), FILTER_VALIDATE_URL) ? 'video_url' : 'video_upload');
                                $currentType = $formData['type_form'] ?? $defaultType;
                                ?>

                                <option value="image" <?= ($currentType === 'image') ? 'selected' : '' ?>>Photo / Image
                                </option>
                                <option value="video_url" <?= ($currentType === 'video_url') ? 'selected' : '' ?>>Vidéo
                                    (URL intégrée)</option>
                                <option value="video_upload" <?= ($currentType === 'video_upload') ? 'selected' : '' ?>>
                                    Vidéo (Téléversement de fichier)</option>

                            </select>
                        </div>

                        <div id="fileUploadBlock" class="mb-4 border p-3 rounded-3 bg-light"
                            style="display: <?= $currentType !== 'video_url' ? 'block' : 'none' ?>;">
                            <h4 class="fw-bold mb-3"><i class="fas fa-file-upload me-2"></i> Fichier actuel et
                                modification</h4>

                            <?php if ($media->getType() === 'image' || ($media->getType() === 'video' && !filter_var($media->getFichier(), FILTER_VALIDATE_URL))): ?>
                                <div class="alert alert-info py-2 mb-3">
                                    Média actuel : <?= $media->getType() === 'image' ? 'Image' : 'Vidéo (Fichier)' ?>
                                </div>
                                <?php if ($media->getType() === 'image'): ?>
                                    <img src="/<?= htmlspecialchars($media->getFichier()) ?>" alt="Aperçu"
                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 5px;"
                                        class="img-thumbnail mb-3">
                                <?php elseif ($media->getType() === 'video'): ?>
                                    <i class="fas fa-play-circle text-danger mb-3" style="font-size: 3rem;"></i>
                                <?php endif; ?>
                            <?php endif; ?>

                            <label for="fichier_media" class="form-label fw-semibold text-muted mb-3"
                                id="fileUploadLabel">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Choisir un nouveau fichier pour le
                                remplacer (Laisser vide pour garder l'actuel)
                                <br><small class="fw-normal text-secondary" id="fileUploadFormats">Formats acceptés :
                                    JPG, PNG, GIF | Taille max : 5Mo</small>
                            </label>

                            <input type="file" name="fichier_media" id="fichier_media"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                accept="image/*,video/*">
                        </div>

                        <div id="videoUrlBlock" class="mb-4 border p-3 rounded-3 bg-light"
                            style="display: <?= $currentType === 'video_url' ? 'block' : 'none' ?>;">
                            <h4 class="fw-bold mb-3"><i class="fab fa-youtube me-2"></i> URL de la Vidéo</h4>

                            <div class="form-floating">
                                <input type="url" name="url_video" id="url_video"
                                    class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                    placeholder="URL de la vidéo (ex: Youtube, Vimeo)"
                                    value="<?= htmlspecialchars($formData['url_video'] ?? (($media->getType() === 'video' && filter_var($media->getFichier(), FILTER_VALIDATE_URL)) ? $media->getFichier() : '')) ?>">
                                <label for="url_video"><i class="fas fa-link me-1"></i> Nouvelle URL</label>
                            </div>

                            <?php if ($media->getType() === 'video' && filter_var($media->getFichier(), FILTER_VALIDATE_URL)): ?>
                                <div class="form-text mt-2">
                                    URL actuelle : <a href="<?= htmlspecialchars($media->getFichier()) ?>" target="_blank"
                                        rel="noopener noreferrer">Voir l'URL</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="form-text text-danger mt-3"><?= $error; ?></div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end align-items-center mt-4 pt-3 border-top">
                            <a href="/avva-admin/page/modifier/5"
                                class="btn btn-outline-secondary rounded-pill px-4 me-2">
                                <i class="fas fa-arrow-left me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg rounded-pill px-4 shadow-sm"
                                name="action" value="edit_media">
                                <i class="fas fa-save me-2"></i> Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="upload-progress-container" class="progress" style="
    display:none; 
    height: 20px; 
    width: 250px; 
    position: fixed; 
    top: 20px; 
    right: 20px; 
    z-index: 1050; 
">
    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        0%
    </div>
</div>

<script>
    // Le script JavaScript a été renommé pour refléter la modification (edit)
    document.addEventListener('DOMContentLoaded', function () {

        // --- 1. Éléments du Formulaire de Modification ---
        const form = document.getElementById('editMediaForm');
        if (!form) return;

        const typeMediaSelect = document.getElementById('type_media');
        const fileUploadBlock = document.getElementById('fileUploadBlock');
        const videoUrlBlock = document.getElementById('videoUrlBlock');
        const fileInput = document.getElementById('fichier_media');
        const urlInput = document.getElementById('url_video');
        const fileUploadLabel = document.getElementById('fileUploadLabel');
        const fileUploadFormats = document.getElementById('fileUploadFormats');

        // --- 2. Éléments de la Barre de Progression ---
        const progressBarContainer = document.getElementById('upload-progress-container');
        const progressBar = document.getElementById('upload-progress-bar');
        const cloudUploadIcon = '<i class="fas fa-cloud-upload-alt me-1"></i>';

        // --- 3. Logique d'Affichage Conditionnel (Toggle) ---
        function toggleMediaFields() {
            const selectedType = typeMediaSelect.value;

            // Cacher et retirer l'obligation de tous les champs potentiels
            fileUploadBlock.style.display = 'none';
            videoUrlBlock.style.display = 'none';
            urlInput.removeAttribute('required');

            if (selectedType === 'image') {
                // --- PHOTO / IMAGE (Upload) ---
                fileUploadBlock.style.display = 'block';
                fileInput.setAttribute('accept', 'image/*');

                fileUploadLabel.innerHTML = cloudUploadIcon + ' Choisir un nouveau fichier Image pour le remplacer (Laisser vide pour garder l\'actuel)';
                fileUploadFormats.innerHTML = 'Formats acceptés : JPG, PNG, GIF | Taille max : 5Mo';

            } else if (selectedType === 'video_upload') {
                // --- VIDÉO (Upload) ---
                fileUploadBlock.style.display = 'block';
                fileInput.setAttribute('accept', 'video/*');

                fileUploadLabel.innerHTML = cloudUploadIcon + ' Choisir un nouveau fichier Vidéo pour le remplacer (Laisser vide pour garder l\'actuel)';
                fileUploadFormats.innerHTML = 'Formats acceptés : MP4, MOV, WEBM | Taille max : 50Mo (exemple)';

            } else if (selectedType === 'video_url') {
                // --- VIDÉO (URL) ---
                videoUrlBlock.style.display = 'block';
                urlInput.setAttribute('required', 'required');
            }
        }

        // Appeler la fonction au chargement et au changement
        toggleMediaFields();
        typeMediaSelect.addEventListener('change', toggleMediaFields);

        // --- 4. Logique de Progression d'Upload (AJAX) ---

        // Fonction pour mettre à jour la barre de progression
        function updateProgress(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                const percentRounded = Math.round(percentComplete);

                progressBar.style.width = percentRounded + '%';
                progressBar.setAttribute('aria-valuenow', percentRounded);
                progressBar.textContent = percentRounded + '%';

                if (percentRounded === 100) {
                    progressBar.textContent = 'Traitement serveur...';
                }
            }
        }

        // Interception de l'envoi du formulaire
        form.addEventListener('submit', function (e) {
            const selectedType = typeMediaSelect.value;
            const isFileUploadType = (selectedType === 'image' || selectedType === 'video_upload');
            const fileIsSelected = fileInput.files.length > 0;

            // Si c'est une URL ou si c'est un type d'upload SANS nouveau fichier, on laisse le POST normal.
            if (selectedType === 'video_url' || (isFileUploadType && !fileIsSelected)) {
                // Le navigateur gérera la soumission normale à form.action (l'URL courante)
                return;
            }

            // Si un fichier est sélectionné (seul cas où la barre de progression est pertinente)
            if (isFileUploadType && fileIsSelected) {
                e.preventDefault();

                // Préparation de la barre de progression
                progressBarContainer.style.display = 'block';
                progressBar.textContent = '0%';
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-danger', 'bg-success');
                // Utiliser bg-warning (couleur d'édition)
                progressBar.classList.add('bg-warning', 'progress-bar-animated');

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener("progress", updateProgress);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        progressBar.classList.remove('progress-bar-animated', 'bg-warning');

                        if (xhr.status >= 200 && xhr.status < 300) {
                            // Succès (200 ou 201)

                            let response;
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (e) {
                                // Fallback si le serveur n'a pas répondu en JSON
                                response = { message: "Succès (Redirection automatique)", redirect: form.action };
                            }

                            progressBar.classList.add('bg-success');
                            progressBar.textContent = response.message || 'Succès !';

                            // Redirection (peut rediriger vers la page de modification elle-même ou la liste)
                            setTimeout(() => {
                                window.location.href = response.redirect || form.action;
                            }, 1000);

                        } else {
                            // Erreur (4xx ou 5xx)
                            let errorMsg = `Erreur (${xhr.status}) lors du traitement.`;
                            try {
                                const response = JSON.parse(xhr.responseText);
                                errorMsg = response.error || errorMsg;
                            } catch (e) {
                                // La réponse n'est pas JSON (ex: erreur 404/500 pure HTML)
                            }

                            progressBar.classList.add('bg-danger');
                            progressBar.textContent = 'Échec: ' + errorMsg;

                            // Afficher l'erreur (dans l'alert ou en la réinjectant dans le DOM)
                            // Laisser le message visible dans la barre ou utiliser l'alert

                            // Masquer après un délai pour laisser le temps de lire l'erreur
                            setTimeout(() => {
                                progressBarContainer.style.display = 'none';
                            }, 3000);
                        }
                    }
                };

                // Utilise l'action du formulaire (l'URL courante)
                xhr.open("POST", form.action, true);
                xhr.send(formData);
            }
        });
    });
</script>