<div class="container">
    <div class="row flex">
        <div class="col py-3">
            <div class="mt-5 mb-5">
                <h2 class="text-center mb-5">
                    <i class="fas fa-edit me-2 text-warning"></i> Modifier le PDF :
                    <?= htmlspecialchars($pdf->getNom()) ?>
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
                    <form id="editPDFForm" method="post" action="" enctype="multipart/form-data">

                        <h3 class="card-title mb-4 text-warning fw-bold">
                            <i class="fas fa-info-circle me-2"></i> Informations générales
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
                                placeholder="Nom du PDF"
                                value="<?= htmlspecialchars($formData['nom'] ?? $pdf->getNom()) ?>" required>
                            <label for="nom_pdf"><i class="fas fa-tag me-1"></i> Nom du PDF</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea name="description_pdf" id="description_pdf"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                placeholder="Description du PDF" style="min-height: 200px;"
                                required><?= htmlspecialchars($formData['description'] ?? $pdf->getDescription()) ?></textarea>
                            <label for="nom_pdf"><i class="fas fa-circle-info me-1"></i> Description du PDF</label>
                        </div>

                        <div id="fileUploadBlock" class="mb-4 border p-3 rounded-3 bg-light">
                            <h4 class="fw-bold mb-3"><i class="fas fa-file-upload me-2"></i> Fichier actuel et
                                modification</h4>

                            <?php if (!filter_var($pdf->getFichier(), FILTER_VALIDATE_URL)): ?>
                                <div class="alert alert-info py-2 mb-3">
                                    PDF actuel :
                                </div>
                                <div class="pdf-wrapper" style="cursor: pointer; width: 70px; height: 70px;"
                                    onclick="openPdfModal('<?= htmlspecialchars($pdf->getFichier()) ?>')">
                                    <canvas class="pdf-preview" data-url="/<?= htmlspecialchars($pdf->getFichier()) ?>"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px; border: 1px solid #ccc;">
                                    </canvas>
                                </div>
                            <?php endif; ?>

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
                            <button type="submit" class="btn btn-warning btn-lg rounded-pill px-4 shadow-sm"
                                name="action" value="edit_pdf">
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
        const form = document.getElementById('editPdfForm');
        if (!form) return;

        const thematiquePdfSelect = document.getElementById('thematique_pdf');
        const fileUploadBlock = document.getElementById('fileUploadBlock');
        const videoUrlBlock = document.getElementById('videoUrlBlock');
        const fileInput = document.getElementById('fichier_pdf');
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Fonction pour ouvrir la modal
    function openPdfModal(url) {
        const frame = document.getElementById('pdf-frame');
        frame.src = url + "#toolbar=0"; // Optionnel : cache la barre d'outils
        const myModal = new bootstrap.Modal(document.getElementById('pdfViewModal'));
        myModal.show();
    }

    // Générer les miniatures au chargement de la page
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.pdf-preview').forEach(canvas => {
            const url = canvas.getAttribute('data-url');

            pdfjsLib.getDocument(url).promise.then(pdf => {
                pdf.getPage(1).then(page => {
                    const viewport = page.getViewport({ scale: 0.3 });
                    const context = canvas.getContext('2d');

                    // On ajuste la résolution interne pour que ce soit net
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                });
            }).catch(err => {
                console.warn("Impossible de charger l'aperçu pour : " + url);
            });
        });

        // Nettoyer l'iframe quand on ferme la modal pour libérer la mémoire
        document.getElementById('pdfViewModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('pdf-frame').src = '';
        });
    });
</script>