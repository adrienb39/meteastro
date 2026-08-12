<?php if ($page->getId() != 5 && $page->getId() != 8): ?>
    <div class="container">
        <div class="row flex">
            <div class="col py-3">
                <div class="mt-5 mb-5">
                    <h2 class="text-center">Insérer une vidéo dans la page : <?= $page->getNom() ?></h2>
                <?php endif; ?>

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

                <div class="mt-5 mb-5 mx-auto">
                    <form id="editPageForm" method="post" enctype="multipart/form-data"
                        class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">

                        <h3
                            class="card-title mb-4 text-primary fw-bold <?= $page->getId() == 6 ? 'd-none' : 'd-block' ?>">
                            <i class="bi bi-film me-2"></i> Insertion d'une vidéo dans la page
                        </h3>

                        <div class="form-floating mb-3">
                            <input type="text" name="nom_contenu_page" id="nom_contenu_page"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                placeholder="Nom du contenu" value="" required>
                            <label for="nom_contenu_page"><i class="bi bi-tag me-1"></i> Nom du contenu</label>
                        </div>

                        <div class="mb-4">
                            <label for="fichier_media" class="form-label fw-semibold text-muted">
                                <i class="bi bi-camera-video me-1"></i> Choisir le fichier vidéo
                            </label>
                            <input type="file" name="fichier_media" id="fichier_media"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                accept="video/mp4,video/x-m4v,video/*" required>
                            <div class="form-text">Formats recommandés : MP4, WebM (Max : vérifiez la config de votre
                                serveur).</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" name="action" value="save">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Télécharger la vidéo
                        </button>

                        <?php if ($page->getId() == 6): ?>
                            <div id="calendar-admin" class="mt-5 border p-3 rounded-4 bg-white shadow"></div>
                        <?php endif; ?>

                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="form-text text-danger mt-3"><?= $error; ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>