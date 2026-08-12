<?php if ($page->getId() != 5 && $page->getId() != 8): ?>
    <div class="container">
        <div class="row flex">
            <div class="col py-3">
                <div class="mt-5 mb-5">
                    <?php if ($page->getId() != 7): ?>
                        <h2 class="text-center">Modifier la page <?= $page->getNom() ?></h2>
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
                        <?php if ($page->getId() != 7): ?>
                            <form id="editPageForm" method="post" class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white"
                                enctype="multipart/form-data">
                                <h3
                                    class="card-title mb-4 text-primary fw-bold <?= $page->getId() == 6 ? 'd-block' : 'd-block' ?>">
                                    <i class="bi bi-file-earmark-text me-2"></i> Édition de la page
                                </h3>

                                <div class="form-floating mb-3 <?= $page->getId() == 6 ? 'd-block' : 'd-block' ?>">
                                    <input type="text" name="nom_page" id="nom_page"
                                        class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                        placeholder="Nom de la page" value="<?= htmlspecialchars($page->getNom()) ?>" required>
                                    <label for="nom_page"><i class="bi bi-tag me-1"></i> Nom de la page</label>
                                </div>

                                <div class="form-floating mb-3 <?= $page->getId() == 6 ? 'd-block' : 'd-block' ?>">
                                    <input type="text" name="url_page" id="url_page"
                                        class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                        placeholder="Url de la page" value="<?= htmlspecialchars($page->getUrl()) ?>" required>
                                    <label for="url_page"><i class="bi bi-link-45deg me-1"></i> URL de la page</label>
                                </div>

                                <div class="mb-4 <?= $page->getId() == 6 ? 'd-block' : 'd-block' ?>">
                                    <label class="form-label fw-semibold text-muted mb-3">
                                        <i class="fa-solid fa-th-large me-1"></i> Actions
                                    </label>

                                    <div class="row g-3 <?= $page->getId() == 6 ? 'd-flex' : 'd-flex' ?>">
                                        <div class="col-md-6">
                                            <a class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center shadow-sm"
                                                href="/avva-admin/page/modifier/<?= $page->getId() ?>/contenu-texte"
                                                data-bs-toggle="tooltip" title="Insérer un texte dans la page.">
                                                <i class="fa-solid fa-plus me-2"></i> Insérer un texte
                                            </a>
                                        </div>

                                        <div class="col-md-6">
                                            <a class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center shadow-sm"
                                                href="/avva-admin/page/modifier/<?= $page->getId() ?>/contenu-image"
                                                data-bs-toggle="tooltip" title="Insérer une image dans la page.">
                                                <i class="fa-solid fa-plus me-2"></i> Insérer une image
                                            </a>
                                        </div>

                                        <div class="col-md-6">
                                            <a class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center shadow-sm"
                                                href="/avva-admin/page/modifier/<?= $page->getId() ?>/contenu-video"
                                                data-bs-toggle="tooltip" title="Insérer une vidéo dans la page.">
                                                <i class="fa-solid fa-plus me-2"></i> Insérer une vidéo
                                            </a>
                                        </div>

                                        <div class="col-md-6">
                                            <a class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center shadow-sm"
                                                href="/avva-admin/page/modifier/<?= $page->getId() ?>/contenu-pdf"
                                                data-bs-toggle="tooltip" title="Insérer un pdf dans la page.">
                                                <i class="fa-solid fa-plus me-2"></i> Insérer un pdf
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 d-none <?= $page->getId() == 6 ? 'd-none' : 'd-block' ?>">
                                    <label for="contenu_page" class="form-label fw-semibold text-muted">
                                        <i class="bi bi-card-text me-1"></i> Contenu de la page
                                    </label>
                                    <textarea name="contenu_page" id="contenu_page"
                                        class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                        style="min-height: 200px;"
                                        required><?= htmlspecialchars($page->getContenu()) ?></textarea>
                                </div>

                                <div class="mb-4 <?= $page->getId() == 6 ? 'd-block' : 'd-block' ?>">
                                    <label for="disposition_page_accueil" class="form-label fw-semibold text-muted">
                                        <i class="bi bi-layout-text-sidebar me-1"></i> Disposition de la page
                                    </label>
                                    <select class="form-select <?= isset($error) ? 'border-danger' : '' ?>"
                                        id="disposition_page_accueil" name="disposition_page_accueil" required>
                                        <option value="">-- Sélectionner une disposition --</option>
                                        <?php
                                        $currentDispositionId = $page->getDispositionPageAccueil() ? $page->getDispositionPageAccueil()->getId() : null;
                                        foreach ($dispositionsPageAccueil as $dispositionPageAccueil) {
                                            $selected = ($dispositionPageAccueil->getId() == $currentDispositionId) ? 'selected' : '';
                                            echo "<option value=\"{$dispositionPageAccueil->getId()}\" {$selected}>{$dispositionPageAccueil->getNom()}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <label for="fichier_media_gauche"
                                                    class="form-label d-flex align-items-center fw-bold text-dark">
                                                    <span class="badge bg-primary-soft text-primary me-2">1</span>
                                                    <i class="bi bi-image me-2"></i> Image Gauche
                                                </label>

                                                <?php if ($page->getImageGauche()): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block mb-2 italic">Aperçu actuel :</small>
                                                        <img src="/<?= htmlspecialchars($page->getImageGauche()) ?>"
                                                            alt="Aperçu gauche"
                                                            style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px; border: 1px solid #dee2e6;"
                                                            class="img-thumbnail">
                                                    </div>
                                                <?php endif; ?>

                                                <div class="input-group">
                                                    <input type="file" name="fichier_media_gauche" id="fichier_media_gauche"
                                                        class="form-control <?= isset($error) ? 'is-invalid' : '' ?>"
                                                        accept="image/jpeg, image/png, image/webp">
                                                </div>

                                                <div class="form-text mt-2">
                                                    <i class="bi bi-info-circle me-1"></i> Position : À gauche du titre.
                                                    <span class="d-block text-muted small">JPG, PNG ou WEBP uniquement.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <label for="fichier_media_droite"
                                                    class="form-label d-flex align-items-center fw-bold text-dark">
                                                    <span class="badge bg-primary-soft text-primary me-2">2</span>
                                                    <i class="bi bi-image-fill me-2"></i> Image Droite
                                                </label>

                                                <?php if ($page->getImageDroite()): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block mb-2 italic">Aperçu actuel :</small>
                                                        <img src="/<?= htmlspecialchars($page->getImageDroite()) ?>"
                                                            alt="Aperçu droite"
                                                            style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px; border: 1px solid #dee2e6;"
                                                            class="img-thumbnail">
                                                    </div>
                                                <?php endif; ?>

                                                <div class="input-group">
                                                    <input type="file" name="fichier_media_droite" id="fichier_media_droite"
                                                        class="form-control <?= isset($error) ? 'is-invalid' : '' ?>"
                                                        accept="image/jpeg, image/png, image/webp">
                                                </div>

                                                <div class="form-text mt-2">
                                                    <i class="bi bi-info-circle me-1"></i> Position : À droite du titre.
                                                    <span class="d-block text-muted small">JPG, PNG ou WEBP uniquement.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" name="action" value="save">Sauvegarder</button>

                                <?php if ($page->getId() == 6): ?>
                                    <div id="calendar-admin" class="mt-5 border p-3 rounded-4 bg-white shadow"></div>
                                <?php endif; ?>

                                <!-- Affichage des erreurs -->
                                <?php if (isset($error) && $error != ""): ?>
                                    <div id="errorHelp" class="form-text text-danger"><?= $error; ?></div>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>

                        <?php if ($page->getId() != 7): ?>
                            <div class="mt-5 <?= $page->getId() == 6 ? 'd-none' : 'd-block' ?>">
                                <h3 class="text-secondary mb-3"><i class="bi bi-tv me-2"></i> Aperçu en temps réel</h3>
                                <iframe id="livePreview" class="rounded-3 shadow-lg border border-secondary-subtle"
                                    style="width:100%; min-height:600px;"></iframe>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($contenus)): ?>
                            <div class="table-responsive bg-white rounded-4 shadow-sm mt-5">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 5%;">#</th>
                                            <th scope="col" style="width: 20%;">Nom du contenu</th>
                                            <th scope="col" style="width: 20%;">Type du contenu</th>
                                            <th scope="col" style="width: 30%;">Ordre du contenu</th>
                                            <th scope="col" style="width: 15%;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        <?php foreach ($contenus as $contenu): ?>
                                            <tr>
                                                <th scope="row"><?= $i++ ?></th>
                                                <td><?= htmlspecialchars($contenu->getNom()) ?></td>
                                                <?php if ($contenu->getTexte()): ?>
                                                    <td>Texte</td>
                                                <?php endif; ?>
                                                <?php if ($contenu->getImage()): ?>
                                                    <td>Image</td>
                                                <?php endif; ?>
                                                <?php if ($contenu->getVideo()): ?>
                                                    <td>Vidéo</td>
                                                <?php endif; ?>
                                                <?php if ($contenu->getPdf()): ?>
                                                    <td>PDF</td>
                                                <?php endif; ?>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group" aria-label="Déplacer la page">
                                                        <?php if ($contenu->getOrdre() > 1): ?>
                                                            <a href="/avva-admin/page/ordre/monter/contenu-page/<?= $contenu->getId() ?>"
                                                                class="btn btn-sm btn-outline-primary rounded-pill me-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Monter ce contenu dans la liste">
                                                                <i class="fa-solid fa-arrow-up"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($contenu->getOrdre() < $ordreMaximum): // Utilise $max_order global ?>
                                                            <a href="/avva-admin/page/ordre/descendre/contenu-page/<?= $contenu->getId() ?>"
                                                                class="btn btn-sm btn-outline-primary rounded-pill"
                                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                                title="Descendre ce contenu dans la liste">
                                                                <i class="fa-solid fa-arrow-down"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($contenu->getTexte()): ?>
                                                        <a href="/avva-admin/page/modifier-contenu-page-texte/<?= $contenu->getId() ?>"
                                                            class="btn btn-sm btn-outline-primary me-2" title="Modifier">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($contenu->getImage()): ?>
                                                        <a href="/avva-admin/page/modifier-contenu-page-image/<?= $contenu->getId() ?>"
                                                            class="btn btn-sm btn-outline-primary me-2" title="Modifier">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($contenu->getVideo()): ?>
                                                        <a href="/avva-admin/page/modifier-contenu-page-video/<?= $contenu->getId() ?>"
                                                            class="btn btn-sm btn-outline-primary me-2" title="Modifier">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($contenu->getPdf()): ?>
                                                        <a href="/avva-admin/page/modifier-contenu-page-pdf/<?= $contenu->getId() ?>"
                                                            class="btn btn-sm btn-outline-primary me-2" title="Modifier">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="/avva-admin/page/supprimer-contenu-page/<?= $contenu->getId() ?>"
                                                        class="btn btn-sm btn-outline-danger btn-supprimer-contenu"
                                                        title="Supprimer" data-bs-toggle="modal" data-bs-target="#modalSuppression"
                                                        data-url="/avva-admin/page/supprimer-contenu-page/<?= $contenu->getId() ?>"
                                                        data-nom-contenu="<?= htmlspecialchars($contenu->getNom()) ?>">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info border-0 rounded-4 shadow-sm p-4 mt-5" role="alert">
                                <h4 class="alert-heading"><i class="fa-solid fa-info-circle me-2"></i> Aucun contenu associé à
                                    la page</h4>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalSuppression" tabindex="-1" aria-labelledby="modalSuppressionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalSuppressionLabel">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        Confirmation de suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer le contenu associé à la page : <br>
                    <strong id="modalContenuNom"></strong> ?
                    <p class="mt-3 text-danger"><i class="fa-solid fa-circle-exclamation me-1"></i> Cette action est
                        irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="btnConfirmerSuppression" href="#" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can me-1"></i> Confirmer la suppression
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var modalSuppression = document.getElementById('modalSuppression');

            modalSuppression.addEventListener('show.bs.modal', function (event) {
                // Bouton qui a déclenché la modale
                var button = event.relatedTarget;

                // Récupérer les informations stockées dans les attributs data-*
                var urlSuppression = button.getAttribute('data-url');
                var nomContenu = button.getAttribute('data-nom-contenu');

                // Mettre à jour le titre de la sortie dans le corps de la modale
                var modalContenuNom = modalSuppression.querySelector('#modalContenuNom');
                modalContenuNom.textContent = nomContenu;

                // Mettre à jour l'URL du bouton de confirmation
                var btnConfirmer = modalSuppression.querySelector('#btnConfirmerSuppression');
                btnConfirmer.href = urlSuppression;
            });
        });
    </script>
    <!-- Modal -->
    <div id="eventModal" class="modal modal-admin">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Modifier l'événement</h3>
                <span id="closeModal" class="close">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalEventId">
                <div class="form-group">
                    <label>Titre :</label>
                    <input type="text" id="modalTitle" placeholder="Titre de l'événement">
                </div>
                <div class="form-group">
                    <label>Description :</label>
                    <textarea id="modalDescription" placeholder="Description de l'événement"></textarea>
                </div>
                <div class="form-group">
                    <label>Catégorie :</label>
                    <select id="modalCategorie">
                        <option value="" disabled selected>Choisissez une catégorie</option>
                        <?php foreach ($categoriesEvent as $cat): ?>
                            <option value="<?= $cat->getId() ?>"><?= $cat->getNom() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group date-group">
                    <div>
                        <label>Début :</label>
                        <input type="datetime-local" id="modalStart">
                    </div>
                    <div>
                        <label>Fin :</label>
                        <input type="datetime-local" id="modalEnd">
                    </div>
                </div>
                <div class="form-group compte-rendu-group">
                    <label>Compte rendu :</label>
                    <textarea id="modalCompteRendu" placeholder="Compte rendu de l'événement"></textarea>
                </div>
                <div class="form-group gpx-group">
                    <label>Fichier GPX (Optionnel) :</label>

                    <div id="gpxDropZone" class="drop-zone">
                        <input type="file" id="modalGpxFile" accept=".gpx" hidden>
                        <div class="drop-zone-content">
                            <i class="fas fa-file-upload"></i>
                            <p>Glissez-déposez un fichier GPX ici</p>
                            <p>ou</p>
                            <button type="button" class="btn-browse">Parcourir</button>
                        </div>
                    </div>

                    <div id="gpxStatusContainer" class="gpx-status-container">
                        <p id="currentGpxFile" class="gpx-status-text"></p>
                        <button type="button" id="removeGpxFile" class="btn-remove-gpx" style="display: none;">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="saveEvent" class="btn-save">Enregistrer</button>
                <button id="deleteEvent" class="btn-delete">Supprimer</button>
                <!-- Modal de confirmation suppression -->
                <div class="modal fade modal-admin" id="confirmDeleteModal" tabindex="-1"
                    aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteLabel">Confirmer la
                                    suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                Êtes-vous sûr de vouloir supprimer cet événement ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div id="notificationToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div id="notificationMessage" class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    <div id="upload-progress-container" class="progress" style="
    display:none; 
    height: 20px; 
    width: 250px; /* Taille pour qu'elle soit visible */
    position: fixed; /* Rendre l'élément fixe */
    top: 20px; /* Distance par rapport au haut de l'écran */
    right: 20px; /* Distance par rapport à la droite de l'écran */
    z-index: 1050; /* S'assurer qu'elle est au-dessus de tout le reste (comme Summernote modals) */
">
        <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
            style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            0%
        </div>
    </div>
<?php elseif ($page->getId() == 5): ?>
    <div class="container">
        <div class="row flex">
            <div class="col py-3">
                <div class="mt-5 mb-5">
                    <h2 class="text-center mb-5">
                        <i class="fas fa-images me-2 text-primary"></i> Gestion des Photos & Vidéos
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

                    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h3 class="card-title text-dark fw-bold mb-0">
                                <i class="fas fa-list-alt me-2"></i> Liste des médias
                            </h3>
                            <a href="/avva-admin/page/creer-photo-video"
                                class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-plus-circle me-2"></i> Ajouter un média
                            </a>
                        </div>

                        <?php if (!empty($medias)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Aperçu</th>
                                            <th scope="col">Titre</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Date d'ajout</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0 ?>
                                        <?php foreach ($medias as $media): ?>
                                            <tr>
                                                <th scope="row"><?= $i += 1 ?></th>
                                                <td>
                                                    <?php if ($media->getType() === 'image'): ?>
                                                        <img src="/<?= htmlspecialchars($media->getFichier()) ?>" alt="Aperçu"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 5px;"
                                                            class="img-thumbnail">
                                                    <?php elseif ($media->getType() === 'video'): ?>
                                                        <i class="fas fa-play-circle text-danger" style="font-size: 3rem;"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($media->getTitre()) ?></td>
                                                <td>
                                                    <?php if ($media->getType() === 'image'): ?>
                                                        <span class="badge bg-success"><i class="fas fa-image"></i> Photo</span>
                                                    <?php elseif ($media->getType() === 'video'): ?>
                                                        <span class="badge bg-info"><i class="fas fa-video"></i> Vidéo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $media->getDateAjout() ? $media->getDateAjout()->format('d/m/Y') : 'N/A' ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="/avva-admin/page/modifier-photo-video/<?= $media->getId() ?>"
                                                        class="btn btn-sm btn-outline-warning me-2" title="Modifier">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteMediaModal<?= $media->getId() ?>" title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i> Aucun média (photo ou vidéo) n'a été trouvé.
                                <br>
                                <a href="/avva-admin/page/creer-photo-video" class="alert-link mt-2 d-inline-block">Cliquez ici
                                    pour en ajouter un.</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($medias as $media): ?>
        <div class="modal fade" id="deleteMediaModal<?= $media->getId() ?>" tabindex="-1"
            aria-labelledby="deleteMediaModalLabel<?= $media->getId() ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMediaModalLabel<?= $media->getId() ?>">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer le média <?= htmlspecialchars($media->getTitre()) ?> ? Cette action
                        est irréversible.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="/avva-admin/page/supprimer-photo-video/<?= $media->getId() ?>" method="POST"
                            style="display:inline;">
                            <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php elseif ($page->getId() == 8): ?>
    <div class="container">
        <div class="row flex">
            <div class="col py-3">
                <div class="mt-5 mb-5">
                    <h2 class="text-center mb-5">
                        <i class="fas fa-file-pdf me-2 text-primary"></i> Gestion des PDFs de la page Comment adhérer
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

                    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h3 class="card-title text-dark fw-bold mb-0">
                                <i class="fas fa-list-alt me-2"></i> Liste des PDFs
                            </h3>
                            <a href="/avva-admin/page/creer-pdf" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-plus-circle me-2"></i> Ajouter un PDF
                            </a>
                        </div>

                        <?php if (!empty($fichiersPdf)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Aperçu</th>
                                            <th scope="col">Thématique</th>
                                            <th scope="col">Nom</th>
                                            <th scope="col">Affiché</th>
                                            <th scope="col">Téléch.</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0;
                                        foreach ($fichiersPdf as $pdf): ?>
                                            <tr>
                                                <th scope="row"><?= ++$i ?></th>
                                                <td>
                                                    <div class="pdf-wrapper" style="cursor: pointer; width: 70px; height: 70px;"
                                                        onclick="openPdfModal('/<?= htmlspecialchars($pdf->getFichier()) ?>')">
                                                        <canvas class="pdf-preview"
                                                            data-url="/<?= htmlspecialchars($pdf->getFichier()) ?>"
                                                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px; border: 1px solid #ccc;">
                                                        </canvas>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($pdf->getThematique()) ?></td>
                                                <td><?= htmlspecialchars($pdf->getNom()) ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?= $pdf->getEstAfficher() ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= $pdf->getEstAfficher() ? 'Oui' : 'Non' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?= $pdf->getEstTelechargeable() ? 'bg-info' : 'bg-secondary' ?>">
                                                        <?= $pdf->getEstTelechargeable() ? 'Oui' : 'Non' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="/avva-admin/page/modifier-pdf/<?= $pdf->getId() ?>"
                                                        class="btn btn-sm btn-outline-warning me-1" title="Modifier">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deletePdfModal<?= $pdf->getId() ?>"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i> Aucun PDF n'a été trouvé.
                                <br>
                                <a href="/avva-admin/page/creer-pdf" class="alert-link mt-2 d-inline-block">Cliquez ici
                                    pour en ajouter un.</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($fichiersPdf as $pdf): ?>
        <div class="modal fade" id="deletePdfModal<?= $pdf->getId() ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="position: relative !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePdfModalLabel<?= $pdf->getId() ?>">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer le PDF <?= htmlspecialchars($pdf->getNom()) ?> ? Cette action
                        est irréversible.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="/avva-admin/page/supprimer-pdf/<?= $pdf->getId() ?>" method="POST"
                            style="display:inline;">
                            <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="modal fade" id="pdfViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="position: relative !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aperçu du document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <iframe id="pdf-frame" src="" width="100%" height="100%" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
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
<?php endif; ?>