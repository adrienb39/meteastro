<div class="container">
    <div class="row flex">
        <div class="col py-3">

            <?php if ($contenu->getPage()->getId() != 5 && $contenu->getPage()->getId() != 8): ?>
                <div class="mt-5 mb-4">
                    <h2 class="text-center">Modifier le texte de la page :
                        <?= htmlspecialchars($contenu->getPage()->getNom()) ?>
                    </h2>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="mt-4 mb-5 mx-auto" style="max-width: 900px;">
                <form id="editPageForm" method="post" class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">

                    <h3
                        class="card-title mb-4 text-primary fw-bold <?= $contenu->getPage()->getId() == 6 ? 'd-none' : 'd-block' ?>">
                        <i class="bi bi-pencil-square me-2"></i> Modification du contenu
                    </h3>

                    <div class="form-floating mb-3">
                        <input type="text" name="nom_contenu_page" id="nom_contenu_page"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" placeholder="Nom du contenu"
                            value="<?= htmlspecialchars($contenu->getNom()) ?>" required>
                        <label for="nom_contenu_page">
                            <i class="bi bi-tag me-1"></i> Nom du contenu
                        </label>
                    </div>

                    <div class="mb-4">
                        <label for="contenu_page" class="form-label fw-semibold text-muted">
                            <i class="bi bi-card-text me-1"></i> Texte du contenu
                        </label>
                        <textarea name="texte_contenu_page" id="contenu_page"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" style="min-height: 300px;"
                            required><?= $contenu->getTexte() ?></textarea>

                        <?php if (isset($error)): ?>
                            <div class="invalid-feedback d-block mt-2"><?= $error; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer les modifications
                        </button>
                        <a href="/avva-admin/page/liste" class="btn btn-outline-secondary px-4 py-2">
                            Annuler
                        </a>
                    </div>

                    <?php if ($contenu->getPage()->getId() == 6): ?>
                        <hr class="my-5">
                        <h4 class="mb-3">Aperçu du calendrier associé</h4>
                        <div id="calendar-admin" class="border p-3 rounded-4 bg-light shadow-sm"></div>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function () {
        $('#contenu_page').summernote({
            placeholder: 'Rédigez le contenu de votre page ici...',
            tabsize: 2,
            height: 300,
            lang: 'fr-FR', // Assurez-vous d'inclure le fichier de langue fr si besoin
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                // Optionnel : Nettoyage automatique du code collé depuis Word
                onPaste: function (e) {
                    var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                    e.preventDefault();
                    document.execCommand('insertText', false, bufferText);
                }
            }
        });
    });
</script>