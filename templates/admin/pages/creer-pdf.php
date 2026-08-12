<div class="container">
    <div class="row flex">
        <div class="col py-3">
            <div class="mt-5 mb-5">
                <h2 class="text-center mb-5">
                    <i class="fas fa-cloud-upload-alt me-2 text-primary"></i> Ajouter un nouveau PDF
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
                    <form id="createPdfForm" method="post" action="" enctype="multipart/form-data">

                        <h3 class="card-title mb-4 text-primary fw-bold">
                            <i class="fas fa-file-pdf me-2"></i> Informations sur le PDF
                        </h3>

                        <div class="mb-4">
                            <label for="thematique_pdf" class="form-label fw-semibold text-muted">
                                <i class="fas fa-list me-1"></i> Thématique du PDF
                            </label>
                            <select class="form-select <?= isset($error) ? 'border-danger' : '' ?>" id="thematique_pdf"
                                name="thematique_pdf" required>

                                <?php
                                $currentThematique = $formData['thematique'];
                                ?>

                                <option value="Bulletin d'adhésion 2026" <?= ($currentThematique === 'Bulletin d\'adhésion 2026') ? 'selected' : '' ?>>Bulletin d'adhésion 2026
                                </option>
                                <option value="Notice d'information" <?= ($currentThematique === 'Notice d\'information') ? 'selected' : '' ?>>Notice d'information</option>
                                <option value="Guide" <?= ($currentThematique === 'Guide') ? 'selected' : '' ?>>Guide
                                </option>
                                <option value="Droit à l'image / Autorisation Parentale" <?= ($currentThematique === 'Droit à l\'image / Autorisation Parentale') ? 'selected' : '' ?>>Droit à l'image /
                                    Autorisation Parentale</option>
                                <option value="Questionnaires de Santé" <?= ($currentThematique === 'Questionnaires de Santé') ? 'selected' : '' ?>>Questionnaires de Santé</option>
                            </select>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" name="nom_pdf" id="nom_pdf"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                placeholder="Nom du PDF" value="<?= htmlspecialchars($formData['nom'] ?? '') ?>"
                                required>
                            <label for="nom_pdf"><i class="fas fa-tag me-1"></i> Nom du PDF</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea name="description_pdf" id="description_pdf"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                placeholder="Description du PDF" style="min-height: 200px;"
                                required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                            <label for="nom_pdf"><i class="fas fa-circle-info me-1"></i> Description du PDF</label>
                        </div>

                        <div id="fileUploadBlock" class="mb-4 border p-3 rounded-3 bg-light">
                            <label for="fichier_pdf" class="form-label fw-semibold text-muted mb-3"
                                id="fileUploadLabel">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Choisir un nouveau fichier pour le
                                remplacer (Laisser vide pour garder l'actuel)
                                <br><small class="fw-normal text-secondary" id="fileUploadFormats">Formats acceptés :
                                    PDF | Taille max : 50Mo</small>
                            </label>

                            <input type="file" name="fichier_pdf" id="fichier_pdf"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                accept="application/pdf">
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input type="checkbox" class="form-check-input" id="est_afficher_pdf"
                                name="est_afficher_pdf" value="" <?= ($formData['estAfficher'] ?? 0) ? 'checked' : ''; ?>>
                            <label for="est_afficher_pdf">Affichage du PDF</label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input type="checkbox" class="form-check-input" id="est_telechargeable_pdf"
                                name="est_telechargeable_pdf" value="" <?= ($formData['estTelechargeable'] ?? 0) ? 'checked' : ''; ?>>
                            <label for="est_telechargeable_pdf">Téléchargeable du PDF</label>
                        </div>

                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="form-text text-danger mt-3"><?= $error; ?></div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end align-items-center mt-4 pt-3 border-top">
                            <a href="/avva-admin/page/modifier/8"
                                class="btn btn-outline-secondary rounded-pill px-4 me-2">
                                <i class="fas fa-arrow-left me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm"
                                name="action" value="create_pdf">
                                <i class="fas fa-save me-2"></i> Enregistrer le PDF
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
    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        0%
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- 1. SÉLECTEURS ET CONFIGURATION ---
        const form = document.getElementById('createPdfForm');
        if (!form) return;

        // Définir l'URL cible (pour éviter le problème de l'attribut action vide)
        // ASSUREZ-VOUS QUE CETTE URL EST LA BONNE POUR VOTRE CONTRÔLEUR POST
        const AJAX_TARGET_URL = "/avva-admin/page/creer-pdf";

        const isCreationMode = form.id === 'createPdfForm';
        const typeMediaSelect = document.getElementById('type_media');
        const fileUploadBlock = document.getElementById('fileUploadBlock');
        const videoUrlBlock = document.getElementById('videoUrlBlock');
        const fileInput = document.getElementById('fichier_media');
        const urlInput = document.getElementById('url_video');
        const fileUploadLabel = document.getElementById('fileUploadLabel');
        const fileUploadFormats = document.getElementById('fileUploadFormats');

        // Éléments de la barre de progression
        const progressBarContainer = document.getElementById('upload-progress-container');
        const progressBar = document.getElementById('upload-progress-bar');
        const cloudUploadIcon = '<i class="fas fa-cloud-upload-alt me-1"></i>';

        // --- 2. LOGIQUE D'AFFICHAGE CONDITIONNEL (TOGGLE) ---

        function toggleMediaFields() {
            const selectedType = typeMediaSelect.value;

            // Réinitialiser / Cacher tout
            fileUploadBlock.style.display = 'none';
            videoUrlBlock.style.display = 'none';
            fileInput.removeAttribute('required');
            urlInput.removeAttribute('required');

            if (selectedType === 'image') {
                // Photo / Image (Upload)
                fileUploadBlock.style.display = 'block';
                if (isCreationMode) fileInput.setAttribute('required', 'required');
                fileInput.setAttribute('accept', 'image/*');
                fileUploadLabel.innerHTML = cloudUploadIcon + ' Téléverser la Photo';
                fileUploadFormats.innerHTML = 'Formats acceptés : JPG, PNG, GIF | Taille max : 5Mo';

            } else if (selectedType === 'video_upload') {
                // Vidéo (Upload)
                fileUploadBlock.style.display = 'block';
                if (isCreationMode) fileInput.setAttribute('required', 'required');
                fileInput.setAttribute('accept', 'video/*');
                fileUploadLabel.innerHTML = cloudUploadIcon + ' Téléverser la Vidéo';
                fileUploadFormats.innerHTML = 'Formats acceptés : MP4, MOV, WEBM | Taille max : 50Mo (exemple)';

            } else if (selectedType === 'video_url') {
                // Vidéo (URL)
                videoUrlBlock.style.display = 'block';
                urlInput.setAttribute('required', 'required');
            }
        }

        // Initialisation et écouteur
        toggleMediaFields();
        typeMediaSelect.addEventListener('change', toggleMediaFields);

        // --- 3. LOGIQUE DE PROGRESSION ET SUBMISSION AJAX ---

        /**
         * Met à jour la barre de progression
         * @param {ProgressEvent} e 
         */
        function updateProgress(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);

                progressBar.style.width = percentComplete + '%';
                progressBar.setAttribute('aria-valuenow', percentComplete);
                progressBar.textContent = percentComplete + '%';

                if (percentComplete === 100) {
                    progressBar.textContent = 'Traitement serveur...';
                }
            }
        }

        form.addEventListener('submit', function (e) {
            const selectedType = typeMediaSelect.value;
            const isFileUploadType = (selectedType === 'image' || selectedType === 'video_upload');

            // Soumission normale si c'est une URL de vidéo
            if (selectedType === 'video_url') {
                return;
            }

            // Interception et traitement AJAX si c'est un Upload (Image ou Vidéo)
            if (isFileUploadType && isCreationMode) {
                e.preventDefault();

                // Préparation de la barre de progression
                progressBarContainer.style.display = 'block';
                progressBar.textContent = '0%';
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-danger', 'bg-success');
                progressBar.classList.add('bg-primary', 'progress-bar-animated');

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                // Ajout de l'écouteur de progression
                xhr.upload.addEventListener("progress", updateProgress);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        progressBar.classList.remove('progress-bar-animated');

                        if (xhr.status >= 200 && xhr.status < 300) {
                            // Succès (200 ou 201)
                            let response;
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (err) {
                                // En cas de succès sans JSON (mauvaise pratique serveur, mais sécurise la redirection)
                                response = { message: "Succès (Redirection automatique)", redirect: AJAX_TARGET_URL };
                            }

                            progressBar.classList.remove('bg-primary');
                            progressBar.classList.add('bg-success');
                            progressBar.textContent = response.message || 'Succès !';

                            // Redirection
                            setTimeout(() => {
                                // La redirection doit pointer vers l'URL fournie par le serveur, ou un fallback
                                window.location.href = response.redirect || '/avva-admin/page/modifier/5';
                            }, 1000);

                        } else {
                            // Erreur (4xx ou 5xx)
                            let errorMsg = 'Problème serveur (code ' + xhr.status + ')';
                            let serverError = '';
                            try {
                                // Tente de récupérer le message d'erreur du JSON
                                const response = JSON.parse(xhr.responseText);
                                serverError = response.error || '';
                            } catch (err) {
                                // L'erreur 404 est typique ici (réponse non JSON)
                            }

                            progressBar.classList.remove('bg-primary');
                            progressBar.classList.add('bg-danger');
                            progressBar.textContent = 'Échec: ' + (serverError || errorMsg);

                            // Afficher l'erreur puis masquer la barre
                            setTimeout(() => {
                                progressBarContainer.style.display = 'none';
                            }, 3000);
                        }
                    }
                };

                // Envoi de la requête POST vers l'URL du contrôleur
                xhr.open("POST", AJAX_TARGET_URL, true);
                xhr.send(formData);
            }
        });
    });
</script>