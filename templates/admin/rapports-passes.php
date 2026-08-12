<?php
// Note: Le contrôleur doit avoir passé les variables $evenements, $user, $active9.

// --- Début de la vue (contenu principal) ---

if (!isset($_SESSION['user'])): ?>
    <p class="alert alert-danger">Accès non autorisé.</p>
<?php else: ?>
    <?php
    // Récupération et effacement des messages de session
    $success_message = $_SESSION['success_message'] ?? null;
    $error_message = $_SESSION['error_message'] ?? null;
    unset($_SESSION['success_message'], $_SESSION['error_message']);

    // Pour formater les dates en français
    try {
        $dateFormat = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
    } catch (\Exception $e) {
        $dateFormat = null;
    }

    $evenements = $evenements ?? [];
    ?>

    <div class="container-fluid py-5" style="margin-top: 50px;">

        <div class="row mt-4 justify-content-center">
            <div class="col-12 col-lg-10">

                <header class="mb-5">
                    <h1 class="display-5 fw-bold mb-2 text-primary">
                        <i class="fas fa-scroll me-3"></i>
                        Gestion des Comptes Rendus
                    </h1>
                    <p class="lead text-secondary">
                        Gérez et publiez les comptes rendus des événements passés. Chaque événement terminé est listé
                        ci-dessous.
                    </p>
                </header>

                <hr class="mb-5">

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-times-circle me-2"></i> Erreur : <?= htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i> Succès : <?= htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($evenements)): ?>
                    <div class="alert alert-light border-0 shadow-lg mt-5 text-center p-5 rounded-3">
                        <i class="fas fa-calendar-check fa-3x mb-3 text-info"></i>
                        <h4 class="alert-heading fw-bold">Aucun Compte Rendu pour l'instant !</h4>
                        <p class="mb-0">Les événements passés apparaissent ici.</p>
                    </div>
                <?php else: ?>

                    <div class="accordion accordion-flush" id="rapportsAccordion">
                        <?php foreach ($evenements as $key => $event):

                            $dateStart = $event->getDateStart();
                            $dateEnd = $event->getDateEnd();
                            $eventTitle = htmlspecialchars($event->getTitre());
                            $eventDescription = $event->getDescription() ?? 'Aucune description fournie.';
                            $eventCategorie = ($event->getCategorieEvent() && method_exists($event->getCategorieEvent(), 'getNom'))
                                ? htmlspecialchars($event->getCategorieEvent()->getNom())
                                : 'Non spécifiée';
                            $currentCompteRendu = $event->getCompteRendu() ?? '';
                            $eventId = $event->getId();
                            $accordionTarget = "collapse{$eventId}";
                            $accordionHeader = "heading{$eventId}";
                            $publicViewUrl = "/page/evenements/compte-rendu/{$eventId}";

                            $dateDisplay = $dateStart->format('d/m/Y');
                            if ($dateFormat) {
                                $dateDisplay = $dateFormat->format($dateStart->getTimestamp());
                                if ($dateEnd && $dateEnd->format('Y-m-d') !== $dateStart->format('Y-m-d')) {
                                    $dateDisplay .= ' au ' . $dateFormat->format($dateEnd->getTimestamp());
                                }
                            } else if ($dateEnd && $dateEnd->format('Y-m-d') !== $dateStart->format('Y-m-d')) {
                                $dateDisplay .= ' au ' . $dateEnd->format('d/m/Y');
                            }

                            $isPublished = !empty($currentCompteRendu);
                            $cardClass = $isPublished ? 'border-success' : 'border-danger';
                            ?>

                            <div class="card mb-3 shadow-sm <?= $cardClass ?>">
                                <div class="card-header p-0" id="<?= $accordionHeader ?>">
                                    <button class="accordion-button p-3 <?= $isPublished ? '' : 'collapsed' ?>" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#<?= $accordionTarget ?>"
                                        aria-expanded="<?= $isPublished ? 'true' : 'false' ?>"
                                        aria-controls="<?= $accordionTarget ?>">

                                        <div class="d-flex w-100 align-items-center">

                                            <?php if ($isPublished): ?>
                                                <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-exclamation-triangle fa-lg me-3 text-danger"></i>
                                            <?php endif; ?>

                                            <div class="flex-grow-1">
                                                <h5 class="mb-0 fw-bold text-dark"><?= $eventTitle ?></h5>
                                                <span class="badge text-primary-emphasis bg-primary-subtle fw-normal small">
                                                    <i class="fas fa-calendar-day me-1"></i> <?= $dateDisplay ?>
                                                </span>
                                            </div>

                                            <span class="ms-4">
                                                <span
                                                    class="badge rounded-pill me-3 <?= $isPublished ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= $isPublished ? 'Publié' : 'CR Manquant' ?>
                                                </span>
                                            </span>
                                        </div>
                                    </button>
                                </div>
                                <div id="<?= $accordionTarget ?>"
                                    class="accordion-collapse collapse <?= $isPublished ? 'show' : '' ?>"
                                    aria-labelledby="<?= $accordionHeader ?>" data-bs-parent="#rapportsAccordion">

                                    <div class="card-body p-4 bg-light">

                                        <div class="card mb-4 border-dark-subtle">
                                            <div
                                                class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                                                <span class="fw-bold"><i class="fas fa-info-circle me-1"></i> Détails</span>
                                            </div>
                                            <div class="card-body small p-3">
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <span class="fw-bold text-info">Catégorie :</span> <?= $eventCategorie ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <span class="fw-bold d-block mb-1 text-info">Description initiale :</span>
                                                        <div class="p-2 border rounded bg-light text-dark small overflow-auto"
                                                            style="max-height: 100px;">
                                                            <?= $eventDescription; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form action="/avva-admin/comptes-rendus/save" method="POST" id="form-cr-<?= $eventId ?>">

                                            <input type="hidden" name="evenement_id" value="<?= $eventId ?>">

                                            <div class="mb-3">
                                                <label for="compte_rendu_<?= $eventId ?>"
                                                    class="form-label fw-semibold text-secondary">
                                                    Contenu du Compte Rendu
                                                </label>
                                                <textarea class="form-control summernote-editor" id="compte_rendu_<?= $eventId ?>"
                                                    name="compte_rendu"
                                                    rows="12"><?= htmlspecialchars($currentCompteRendu); ?></textarea>
                                            </div>

                                            <div class="d-flex justify-content-between border-top pt-3 mt-3">

                                                <?php if ($isPublished): ?>
                                                    <button type="button" class="btn btn-outline-danger delete-cr-btn shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                        data-form-id="form-cr-<?= $eventId ?>" data-event-title="<?= $eventTitle ?>">
                                                        <i class="fas fa-trash-alt me-2"></i> Supprimer le Compte Rendu
                                                    </button>
                                                <?php else: ?>
                                                    <div></div>
                                                <?php endif; ?>

                                                <button type="submit" class="btn btn-primary shadow-sm">
                                                    <i class="fas fa-save me-2"></i> Enregistrer et Publier
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel"><i
                            class="fas fa-exclamation-triangle me-2"></i> Confirmation de Suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Vous êtes sur le point de supprimer définitivement le compte rendu pour l'événement :</p>
                    <p class="alert alert-warning fw-bold" id="eventTitlePlaceholder"></p>
                    <p>Êtes-vous sûr de vouloir continuer ? Cette action enlevera le compte rendu et le rendra inaccessible
                        au public.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                            class="fas fa-times me-2"></i> Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton"><i
                            class="fas fa-trash-alt me-2"></i> Confirmer la Suppression</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- Initialisation de Summernote ---
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                $('.summernote-editor').summernote({
                    tabsize: 2,
                    height: 300,
                    lang: 'fr-FR',
                    placeholder: 'Compte rendu de l\'événement ici...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            } else {
                console.warn("Summernote n'est pas chargé.");
            }

            // --- GESTION DE LA MODALE DE SUPPRESSION ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');
            let formToSubmit = null; // Variable pour stocker le formulaire à soumettre

            // 1. Quand un bouton "Supprimer" est cliqué (avant l'ouverture de la modale)
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // Le bouton qui a déclenché la modale
                const formId = button.getAttribute('data-form-id');
                const eventTitle = button.getAttribute('data-event-title');

                // Stocke le formulaire pour l'utiliser lors de la confirmation
                formToSubmit = document.getElementById(formId);

                // Met à jour le titre de l'événement dans le corps de la modale
                document.getElementById('eventTitlePlaceholder').textContent = eventTitle;
            });

            // 2. Quand le bouton "Confirmer la Suppression" dans la modale est cliqué
            confirmDeleteButton.addEventListener('click', function () {
                if (formToSubmit) {
                    const eventId = formToSubmit.querySelector('input[name="evenement_id"]').value;
                    const textareaId = 'compte_rendu_' + eventId;

                    // 1. Vider le contenu du Summernote
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                        $('#' + textareaId).summernote('code', '');
                    } else {
                        document.getElementById(textareaId).value = '';
                    }

                    // 2. Cacher la modale
                    const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                    modalInstance.hide();

                    // 3. Soumettre le formulaire
                    formToSubmit.submit();
                }
            });
        });
    </script>
<?php endif; ?>