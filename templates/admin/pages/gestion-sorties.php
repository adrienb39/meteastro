<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12">

            <header class="mb-5 border-bottom pb-3 d-flex justify-content-between align-items-center">
                <h1 class="display-6 fw-bold text-dark">
                    <i class="fa-solid fa-bicycle text-primary me-2"></i> Gestion des Sorties Hebdomadaires
                </h1>
            </header>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="card shadow-lg border-0 rounded-4 mb-5 bg-white">
                <div
                    class="card-header <?= $isEditing ? 'bg-warning text-dark' : 'bg-primary text-white' ?> p-3 rounded-top-4">
                    <h3 class="mb-0 fw-light">
                        <i class="fa-solid fa-<?= $isEditing ? 'pen-to-square' : 'plus-circle' ?> me-2"></i>
                        <?= $isEditing ? 'Modification de la sortie : ' . $titre : 'Ajouter une nouvelle sortie' ?>
                    </h3>
                </div>

                <div class="card-body p-4 p-md-5">

                    <form method="post" action="<?= $actionUrl ?>">

                        <div class="form-floating mb-4">
                            <input type="text" name="titre_sortie" id="titre_sortie" class="form-control"
                                placeholder="Titre de la sortie"
                                value="<?= $sortieEnEdition ? $sortieEnEdition->getTitre() : '' ?>" required>
                            <label for="titre_sortie">Titre de la sortie</label>
                        </div>

                        <div class="mb-4">
                            <label for="description_sortie" class="form-label text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i> Description / Détails de la sortie
                            </label>
                            <textarea name="description_sortie" id="description_sortie" class="form-control"
                                placeholder="Description / Détails de la sortie"
                                data-summernote="true"><?= $sortieEnEdition ? $sortieEnEdition->getDescription() : '' ?></textarea>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="datetime-local" name="date_depart_sortie" id="date_depart_sortie"
                                class="form-control" required
                                value="<?= $sortieEnEdition ? $sortieEnEdition->getDate()->format('Y-m-d\TH:i') : $dateDepart ?>">
                            <label for="date_depart_sortie">
                                <i class="fa-solid fa-calendar-check me-1"></i> Date et heure du départ de la sortie
                            </label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="time" name="temps_sortie" id="temps_sortie" class="form-control" required
                                value="<?= $sortieEnEdition ? $sortieEnEdition->getTemps()->format('H:i') : $temps ?>">
                            <label for="temps_sortie">
                                <i class="fa-solid fa-clock me-1"></i> Temps de la sortie
                            </label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="text" name="difficulte_sortie" id="difficulte_sortie" class="form-control"
                                placeholder="Difficulté de la sortie"
                                value="<?= $sortieEnEdition ? $sortieEnEdition->getDifficulte() : '' ?>" required>
                            <label for="difficulte_sortie">Difficulté de la sortie</label>
                        </div>

                        <?php if (!$sortieEnEdition): ?>
                            <div class="form-floating mb-4">
                                <select class="form-select form-select-lg" id="type_sortie" name="type_sortie[]" multiple
                                    required style="height: auto; min-height: 100px;">
                                    <?php foreach ($typesSorties as $typeSortie): ?>
                                        <option value="<?= $typeSortie->getId() ?>"><?= $typeSortie->getNom() ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="type_sortie" class="form-label text-muted small mb-1">Type(s) de sortie
                                    (Maintenir Ctrl pour plusieurs)</label>
                                <div class="invalid-feedback">Veuillez indiquer au moins un type de sortie.</div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="mt-3 alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid mt-4">
                            <button type="submit"
                                class="btn btn-lg <?= $isEditing ? 'btn-warning' : 'btn-primary' ?> rounded-pill shadow-sm">
                                <i class="fa-solid fa-save me-2"></i>
                                <?= $isEditing ? 'Mettre à jour la sortie' : 'Sauvegarder la nouvelle sortie' ?>
                            </button>

                            <?php if ($isEditing): ?>
                                <a href="/avva-admin/sortie" class="btn btn-outline-secondary mt-2 rounded-pill">
                                    <i class="fa-solid fa-xmark me-2"></i> Annuler la modification et retourner à la liste
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div
                        class="mb-5 mt-5 border-bottom pb-4 d-flex flex-wrap justify-content-center justify-content-md-between align-items-center gap-3">
                        <button class="btn btn-outline-info rounded-pill shadow-sm px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseMessageApresSortieHebdomadaire">
                            <i class="fa-solid fa-flag-checkered me-2"></i>
                            Message de fin de sortie
                        </button>

                        <button class="btn btn-outline-warning text-dark rounded-pill shadow-sm px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseMessageSortieHebdomadaireADefinir">
                            <i class="fa-solid fa-calendar-xmark me-2"></i>
                            Message "Sortie à définir"
                        </button>
                    </div>

                    <?php foreach (['success_message' => 'success', 'error_message' => 'danger'] as $key => $type): ?>
                        <?php if (isset($_SESSION[$key])): ?>
                            <div class="alert alert-<?= $type ?> alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4">
                                <i
                                    class="fa-solid fa-<?= $type === 'success' ? 'circle-check' : 'triangle-exclamation' ?> me-2"></i>
                                <?= $_SESSION[$key];
                                unset($_SESSION[$key]); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div class="collapse mb-5 <?= isset($_POST['message_apres_sortie_hebdomadaire']) ? 'show' : '' ?>"
                        id="collapseMessageApresSortieHebdomadaire">
                        <div class="card shadow-lg border-0 rounded-4 bg-light">
                            <div class="card-header bg-dark text-white p-3 rounded-top-4">
                                <h4 class="mb-0 fw-light"><i class="fa-solid fa-flag-checkered me-2"></i> Message de fin
                                    de sortie</h4>
                            </div>
                            <div class="card-body p-4 bg-light-subtle rounded-bottom-4">
                                <form method="post" action="/avva-admin/sortie/message-fin-sortie" class="position-relative">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="form-group position-relative">
                                                <label
                                                    class="form-label d-flex align-items-center fw-bold text-secondary mb-3">
                                                    <span
                                                        class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 28px; height: 28px;">
                                                        <i class="fa-solid fa-pen-nib fs-6"></i>
                                                    </span>
                                                    Contenu du message personnalisé
                                                </label>

                                                <div
                                                    class="input-group shadow-sm rounded-4 overflow-hidden border-0 transition-all">
                                                    <textarea name="message_apres_sortie_hebdomadaire"
                                                        class="form-control border-0 p-4 fs-5" rows="3"
                                                        style="resize: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px);"
                                                        placeholder="Ex: Bravo à tous pour cette sortie ! Prochain RDV la semaine prochaine..."><?= htmlspecialchars($contenuTexte) ?></textarea>
                                                </div>

                                                <div class="form-text mt-2 ps-2 italic text-muted">
                                                    <i class="fa-solid fa-circle-info me-1"></i>
                                                    Ce message s'affichera automatiquement une fois le temps de la
                                                    sortie écoulé.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center mt-4 gap-3">
                                        <span class="small text-muted d-none d-md-inline">
                                            <i class="fa-solid fa-cloud-upload-alt me-1"></i> Modifications prêtes à
                                            être publiées
                                        </span>
                                        <button type="submit"
                                            class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm hover-lift d-flex align-items-center">
                                            <span>Enregistrer</span>
                                            <i class="fa-solid fa-paper-plane ms-2 small"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <style>
                                /* Petit surplus de CSS pour l'animation et le look moderne */
                                .transition-all {
                                    transition: all 0.3s ease-in-out;
                                }

                                .input-group:focus-within {
                                    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.15) !important;
                                    transform: translateY(-2px);
                                }

                                .hover-lift {
                                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                                }

                                .hover-lift:hover {
                                    transform: translateY(-3px);
                                    box-shadow: 0 8px 15px rgba(13, 110, 253, 0.25) !important;
                                }

                                .form-control:focus {
                                    box-shadow: none !important;
                                }
                            </style>
                        </div>
                    </div>
                    <div class="collapse mb-5 <?= isset($_POST['message_sortie_hebdomadaire_a_definir']) ? 'show' : '' ?>"
                        id="collapseMessageSortieHebdomadaireADefinir">
                        <div class="card shadow-lg border-0 rounded-4 bg-light">
                            <div class="card-header bg-dark text-white p-3 rounded-top-4">
                                <h4 class="mb-0 fw-light"><i class="fa-solid fa-calendar-xmark me-2"></i> Message "Sortie à définir"</h4>
                            </div>
                            <div class="card-body p-4 bg-light-subtle rounded-bottom-4">
                                <form method="post" action="/avva-admin/sortie/message-sortie-a-definir" class="position-relative">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="form-group position-relative">
                                                <label
                                                    class="form-label d-flex align-items-center fw-bold text-secondary mb-3">
                                                    <span
                                                        class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 28px; height: 28px;">
                                                        <i class="fa-solid fa-pen-nib fs-6"></i>
                                                    </span>
                                                    Contenu du message personnalisé
                                                </label>

                                                <div
                                                    class="input-group shadow-sm rounded-4 overflow-hidden border-0 transition-all">
                                                    <textarea name="message_sortie_hebdomadaire_a_definir"
                                                        class="form-control border-0 p-4 fs-5" rows="3"
                                                        style="resize: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px);"
                                                        placeholder=""><?= htmlspecialchars($contenuTexte2) ?></textarea>
                                                </div>

                                                <div class="form-text mt-2 ps-2 italic text-muted">
                                                    <i class="fa-solid fa-circle-info me-1"></i>
                                                    Ce message s'affichera automatiquement qu'il n'y a aucune sortie.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center mt-4 gap-3">
                                        <span class="small text-muted d-none d-md-inline">
                                            <i class="fa-solid fa-cloud-upload-alt me-1"></i> Modifications prêtes à
                                            être publiées
                                        </span>
                                        <button type="submit"
                                            class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm hover-lift d-flex align-items-center">
                                            <span>Enregistrer</span>
                                            <i class="fa-solid fa-paper-plane ms-2 small"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <style>
                                /* Petit surplus de CSS pour l'animation et le look moderne */
                                .transition-all {
                                    transition: all 0.3s ease-in-out;
                                }

                                .input-group:focus-within {
                                    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.15) !important;
                                    transform: translateY(-2px);
                                }

                                .hover-lift {
                                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                                }

                                .hover-lift:hover {
                                    transform: translateY(-3px);
                                    box-shadow: 0 8px 15px rgba(13, 110, 253, 0.25) !important;
                                }

                                .form-control:focus {
                                    box-shadow: none !important;
                                }
                            </style>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="display-6 fw-bold text-dark mt-5 mb-4">
                <i class="fa-solid fa-list-check text-secondary me-2"></i> Sorties Programmées
            </h2>

            <?php if (!empty($sorties)): ?>
                <div class="table-responsive bg-white rounded-4 shadow-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 20%;">Type de sortie</th>
                                <th scope="col" style="width: 30%;">Titre</th>
                                <th scope="col" style="width: 20%;">Départ Prévu</th>
                                <th scope="col" style="width: 10%;">Statut</th>
                                <th scope="col" style="width: 15%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($sorties as $sortie): ?>
                                <tr
                                    class="<?= $isEditing && $sortie->getId() === $sortieEnEdition->getId() ? 'table-warning' : '' ?>">
                                    <th scope="row"><?= $i++ ?></th>
                                    <td>
                                        <?php if ($sortie->getTypesSorties()->isEmpty()): ?>
                                            <span class="text-muted small"><em>Aucun type défini</em></span>
                                        <?php else: ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($sortie->getTypesSorties() as $type): ?>
                                                    <span class="badge bg-secondary opacity-75">
                                                        <?= htmlspecialchars($type->getNom()) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($sortie->getTitre()) ?></td>
                                    <td>
                                        <i class="fa-solid fa-calendar-alt me-1"></i>
                                        <?= $sortie->getDate()->format('d/m/Y') ?>
                                        <span class="badge bg-secondary ms-2">à <?= $sortie->getDate()->format('H:i') ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $now = new \DateTime();
                                        if ($sortie->getDate() > $now) {
                                            echo '<span class="badge bg-success-subtle text-success border border-success">À venir</span>';
                                        } else {
                                            echo '<span class="badge bg-warning-subtle text-warning border border-warning">Passée</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="/avva-admin/sortie/modifier/<?= $sortie->getId() ?>"
                                            class="btn btn-sm btn-outline-primary me-2" title="Modifier">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="/avva-admin/sortie/supprimer/<?= $sortie->getId() ?>"
                                            class="btn btn-sm btn-outline-danger btn-supprimer-sortie" title="Supprimer"
                                            data-bs-toggle="modal" data-bs-target="#modalSuppression"
                                            data-url="/avva-admin/sortie/supprimer/<?= $sortie->getId() ?>"
                                            data-titre-sortie="<?= htmlspecialchars($sortie->getTitre()) ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info border-0 rounded-4 shadow-sm p-4" role="alert">
                    <h4 class="alert-heading"><i class="fa-solid fa-info-circle me-2"></i> Aucune sortie programmée</h4>
                    <p>Utilisez le formulaire ci-dessus pour ajouter votre première sortie hebdomadaire.</p>
                </div>
            <?php endif; ?>

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
                Êtes-vous sûr de vouloir supprimer la sortie : <br>
                <strong id="modalSortieTitre"></strong> ?
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
            var titreSortie = button.getAttribute('data-titre-sortie');

            // Mettre à jour le titre de la sortie dans le corps de la modale
            var modalSortieTitre = modalSuppression.querySelector('#modalSortieTitre');
            modalSortieTitre.textContent = titreSortie;

            // Mettre à jour l'URL du bouton de confirmation
            var btnConfirmer = modalSuppression.querySelector('#btnConfirmerSuppression');
            btnConfirmer.href = urlSuppression;
        });
        // -----------------------------------------------------------

        // Mettre le focus sur le formulaire de modification si nous sommes en mode édition
        <?php if (isset($isEditing) && $isEditing): ?>
            $('html, body').animate({
                scrollTop: $('.card-header.bg-warning').offset().top - 20
            }, 500);
        <?php endif; ?>
    });
</script>