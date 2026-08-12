<div class="container">
    <div class="row flex">
        <div class="col py-3">

            <?php if ($contenu->getPage()->getId() != 5 && $contenu->getPage()->getId() != 8): ?>
                <div class="mt-5 mb-4">
                    <h2 class="text-center">Modifier la vidéo de la page :
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

            <div class="mt-4 mb-5 mx-auto" style="max-width: 850px;">
                <form id="editPageForm" method="post" enctype="multipart/form-data"
                    class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">

                    <h3
                        class="card-title mb-4 text-primary fw-bold <?= $contenu->getPage()->getId() == 6 ? 'd-none' : 'd-block' ?>">
                        <i class="bi bi-pencil-square me-2"></i> Modification d'un contenu vidéo
                    </h3>

                    <div class="form-floating mb-4">
                        <input type="text" name="nom_contenu_page" id="nom_contenu_page"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" placeholder="Nom du contenu"
                            value="<?= htmlspecialchars($contenu->getNom()) ?>" required>
                        <label for="nom_contenu_page"><i class="bi bi-tag me-1"></i> Titre de la vidéo</label>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted d-block mb-3">Vidéo actuelle</label>
                        <div class="ratio ratio-16x9 border rounded-4 bg-dark overflow-hidden shadow-sm">
                            <?php if ($contenu->getVideo()): ?>
                                <video controls preload="metadata">
                                    <source src="<?= $contenu->getVideo() ?>" type="video/mp4">
                                    Votre navigateur ne supporte pas la lecture de vidéos.
                                </video>
                            <?php else: ?>
                                <div class="d-flex flex-column align-items-center justify-content-center text-white-50">
                                    <i class="bi bi-camera-video-off display-4 mb-2"></i>
                                    <p>Aucun fichier vidéo associé</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($contenu->getVideo()): ?>
                            <div class="form-text mt-2 text-center">
                                Fichier actuel : <code><?= basename($contenu->getVideo()) ?></code>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="fichier_media" class="form-label fw-semibold text-muted">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Remplacer la vidéo (Optionnel)
                        </label>
                        <input type="file" name="fichier_media" id="fichier_media"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>"
                            accept="video/mp4,video/x-m4v,video/*">
                        <div class="form-text mt-2">
                            Laissez vide pour conserver la vidéo actuelle. <br>
                            Formats acceptés : <strong>MP4, WebM</strong>.
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer les modifications
                        </button>
                        <a href="/avva-admin/page/liste" class="btn btn-outline-secondary btn-lg px-4">
                            Annuler
                        </a>
                    </div>

                    <?php if ($contenu->getPage()->getId() == 6): ?>
                        <div id="calendar-admin" class="mt-5 border p-3 rounded-4 bg-light shadow-sm"></div>
                    <?php endif; ?>

                    <?php if (isset($error) && $error != ""): ?>
                        <div class="alert alert-danger mt-4 small mb-0">
                            <i class="bi bi-exclamation-octagon me-2"></i><?= $error; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>