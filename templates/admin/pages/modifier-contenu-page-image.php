<div class="container">
    <div class="row flex">
        <div class="col py-3">

            <?php if ($contenu->getPage()->getId() != 5 && $contenu->getPage()->getId() != 8): ?>
                <div class="mt-5 mb-4">
                    <h2 class="text-center">Modifier l'image de la page :
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

            <div class="mt-4 mb-5 mx-auto" style="max-width: 800px;">
                <form id="editPageForm" method="post" enctype="multipart/form-data"
                    class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">

                    <h3
                        class="card-title mb-4 text-primary fw-bold <?= $contenu->getPage()->getId() == 6 ? 'd-none' : 'd-block' ?>">
                        <i class="bi bi-pencil-square me-2"></i> Modification d'une image
                    </h3>

                    <div class="form-floating mb-4">
                        <input type="text" name="nom_contenu_page" id="nom_contenu_page"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" placeholder="Nom du contenu"
                            value="<?= htmlspecialchars($contenu->getNom()) ?>" required>
                        <label for="nom_contenu_page"><i class="bi bi-tag me-1"></i> Nom du contenu</label>
                    </div>

                    <div class="mb-4 text-center p-3 border rounded-3 bg-light">
                        <label class="form-label d-block fw-semibold text-muted mb-3">Image actuelle</label>
                        <?php if ($contenu->getImage()): ?>
                            <img src="<?= $contenu->getImage() ?>" alt="Aperçu" class="img-fluid rounded-3 shadow-sm mb-2"
                                style="max-height: 250px; object-fit: cover;">
                            <div class="small text-muted"><?= basename($contenu->getImage()) ?></div>
                        <?php else: ?>
                            <div class="py-4 text-muted">
                                <i class="bi bi-image-fill display-4 d-block mb-2"></i>
                                Aucune image actuelle
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="fichier_media" class="form-label fw-semibold text-muted">
                            <i class="bi bi-cloud-upload me-1"></i> Remplacer l'image (optionnel)
                        </label>
                        <input type="file" name="fichier_media" id="fichier_media"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" accept="image/*">
                        <div class="form-text mt-2">
                            Formats acceptés : <strong>JPG, PNG, WEBP</strong>.
                            <br>Laissez vide si vous ne souhaitez pas modifier l'image actuelle.
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="/avva-admin/page/liste" class="btn btn-outline-secondary w-100 py-2">
                                Annuler
                            </a>
                        </div>
                    </div>

                    <?php if ($contenu->getPage()->getId() == 6): ?>
                        <div id="calendar-admin" class="mt-5 border p-3 rounded-4 bg-white shadow-sm"></div>
                    <?php endif; ?>

                    <?php if (isset($error) && $error != ""): ?>
                        <div class="alert alert-danger mt-4 py-2 small">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= $error; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>