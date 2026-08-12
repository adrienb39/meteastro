<div class="container">
    <div class="row flex">
        <div class="col py-3">

            <?php if ($contenu->getPage()->getId() != 5 && $contenu->getPage()->getId() != 8): ?>
                <div class="mt-5 mb-4">
                    <h2 class="text-center">Modifier le document : <?= htmlspecialchars($contenu->getPage()->getNom()) ?>
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
                        <i class="bi bi-pencil-square me-2"></i> Modification du PDF
                    </h3>

                    <div class="form-floating mb-4">
                        <input type="text" name="nom_contenu_page" id="nom_contenu_page"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" placeholder="Nom du contenu"
                            value="<?= htmlspecialchars($contenu->getNom()) ?>" required>
                        <label for="nom_contenu_page"><i class="bi bi-tag me-1"></i> Nom du document</label>
                    </div>

                    <div class="mb-4 p-3 border rounded-3 bg-light">
                        <label class="form-label d-block fw-semibold text-muted mb-2">Fichier actuel</label>
                        <?php if ($contenu->getPdf()): ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-1 me-3"></i>
                                    <div class="overflow-hidden">
                                        <div class="text-dark fw-bold text-truncate" style="max-width: 250px;">
                                            <?= basename($contenu->getPdf()) ?>
                                        </div>
                                        <div class="small text-muted">Document en ligne</div>
                                    </div>
                                </div>
                                <a href="<?= $contenu->getPdf() ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i> Voir
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-muted small">
                                <i class="bi bi-exclamation-circle me-1"></i> Aucun fichier PDF associé.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="fichier_media" class="form-label fw-semibold text-muted">
                            <i class="bi bi-file-earmark-arrow-up me-1"></i> Remplacer le document PDF
                        </label>
                        <input type="file" name="fichier_media" id="fichier_media"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" accept="application/pdf">
                        <div class="form-text mt-2">
                            Laissez vide pour conserver le document actuel. Format accepté : <strong>PDF
                                uniquement</strong>.
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Enregistrer les modifications
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
                        <div class="alert alert-danger mt-4 py-2 small mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= $error; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>