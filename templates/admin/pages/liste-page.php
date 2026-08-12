<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-4">
        <h1 class="display-5 fw-bold text-dark">Gestion des Pages <i class="fa-solid fa-file-lines text-primary"></i>
        </h1>
        <a class="btn btn-outline-primary btn-lg d-flex align-items-center rounded-pill shadow-sm"
            href="/avva-admin/defilement" data-bs-toggle="tooltip" data-bs-placement="bottom"
            title="Modifier le texte qui défile sur l'accueil.">
            <i class="fa-solid fa-arrows-left-right me-2"></i>
            Texte défilant
        </a>
        <a class="btn btn-primary btn-lg d-flex align-items-center rounded-pill shadow-sm" href="/avva-admin/page/creer"
            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ajouter une nouvelle page en haut du menu.">
            <i class="fa-solid fa-plus me-2"></i>
            Créer une nouvelle page
        </a>
    </div>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="row g-4 mb-5">

        <div class="col-md-6">
            <h2 class="h4 mb-3 text-start text-dark"><i class="fa-solid fa-circle-arrow-left me-2 text-info"></i>
                Disposition Gauche</h2>
            <div class="card shadow-lg rounded-5 border-0">
                <div class="card-body p-0">
                    <div class="table-responsive rounded-5">
                        <table class="table table-hover align-middle mb-0 table-rounded-top">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col" style="width: 30%;">Nom</th>
                                    <th scope="col" style="width: 30%;">URL</th>
                                    <th scope="col" class="text-center" style="width: 20%;">Ordre</th>
                                    <th scope="col" class="text-center" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php $max_gauche = count($pages_gauche); ?>
                                <?php
                                // Définition locale des IDs des pages fixes. 
                                // ATTENTION : Si ces pages sont dans $pages_gauche, elles doivent être exclues ici.
                                // Nous supposons que $pages_gauche ne contient QUE des pages déplaçables.
                                $FIXED_PAGE_IDS = [1, 2, 3];
                                ?>
                                <?php foreach ($pages_gauche as $page): ?>
                                    <?php
                                    $is_special_page = in_array($page->getId(), $FIXED_PAGE_IDS);
                                    $modify_url = "/avva-admin/page/modifier/" . $page->getId();
                                    ?>
                                    <tr class="align-middle">
                                        <th scope="row" class="text-muted"><?= $i++ ?></th>
                                        <td class="fw-medium"><?= htmlspecialchars($page->getNom()) ?></td>
                                        <td><code><?= htmlspecialchars($page->getUrl()) ?></code></td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Déplacer la page">
                                                <?php if ($page->getOrdrePageAccueil() > 1): ?>
                                                    <a href="/avva-admin/page/ordre/monter/<?= $page->getId() ?>"
                                                        class="btn btn-sm btn-outline-primary rounded-pill me-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Monter cette page dans la liste">
                                                        <i class="fa-solid fa-arrow-up"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($page->getOrdrePageAccueil() < $max_order): // Utilise $max_order global ?>
                                                    <a href="/avva-admin/page/ordre/descendre/<?= $page->getId() ?>"
                                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Descendre cette page dans la liste">
                                                        <i class="fa-solid fa-arrow-down"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($page->getId() == 7 || $page->getId() == 8): ?>
                                                <a href="/avva-admin/randonnee"
                                                    class="btn btn-sm btn-warning text-white rounded-pill"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Modifier le contenu et le nom de la page">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= $modify_url ?>"
                                                    class="btn btn-sm btn-warning text-white rounded-pill"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Modifier le contenu et le nom de la page">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($pages_gauche)): ?>
                            <p class="text-center p-4 text-muted">Aucune page n'est configurée pour la disposition gauche.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2 class="h4 mb-3 text-end text-dark">Disposition Droite <i
                    class="fa-solid fa-circle-arrow-right ms-2 text-info"></i></h2>
            <div class="card shadow-lg rounded-5 border-0">
                <div class="card-body p-0">
                    <div class="table-responsive rounded-5">
                        <table class="table table-hover align-middle mb-0 table-rounded-top">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col" style="width: 30%;">Nom</th>
                                    <th scope="col" style="width: 30%;">URL</th>
                                    <th scope="col" class="text-center" style="width: 20%;">Ordre</th>
                                    <th scope="col" class="text-center" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php $max_droite = count($pages_droite); ?>
                                <?php foreach ($pages_droite as $page): ?>
                                    <?php if ($page->getId() != 5 && $page->getId() != 12 && $page->getId() != 13 && $page->getId() != 14): ?>
                                        <?php $modify_url = "/avva-admin/page/modifier/" . $page->getId(); ?>
                                        <tr class="align-middle">
                                            <th scope="row" class="text-muted"><?= $i++ ?></th>
                                            <td class="fw-medium"><?= htmlspecialchars($page->getNom()) ?></td>
                                            <td><code><?= htmlspecialchars($page->getUrl()) ?></code></td>

                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="Déplacer la page">
                                                    <?php if ($page->getOrdrePageAccueil() > 1): ?>
                                                        <a href="/avva-admin/page/ordre/monter/<?= $page->getId() ?>"
                                                            class="btn btn-sm btn-outline-primary rounded-pill me-1"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Monter cette page dans la liste">
                                                            <i class="fa-solid fa-arrow-up"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($page->getOrdrePageAccueil() < $max_order): // Utilise $max_order global ?>
                                                        <a href="/avva-admin/page/ordre/descendre/<?= $page->getId() ?>"
                                                            class="btn btn-sm btn-outline-primary rounded-pill"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="Descendre cette page dans la liste">
                                                            <i class="fa-solid fa-arrow-down"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($page->getId() == 7): ?>
                                                    <a href="/avva-admin/randonnee"
                                                        class="btn btn-sm btn-warning text-white rounded-pill"
                                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                                        title="Modifier le contenu et le nom de la page">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= $modify_url ?>"
                                                        class="btn btn-sm btn-warning text-white rounded-pill"
                                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                                        title="Modifier le contenu et le nom de la page">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($pages_droite)): ?>
                            <p class="text-center p-4 text-muted">Aucune page n'est configurée pour la disposition droite.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <h2 class="h4 mb-3 mt-4 text-dark"><i class="fa-solid fa-lock me-2 text-danger"></i>
                Pages Spéciales</h2>
            <div class="card shadow-lg rounded-5 border-0">
                <div class="card-body p-0">
                    <div class="table-responsive rounded-5">
                        <table class="table table-hover align-middle mb-0 table-rounded-top">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col" style="width: 35%;">Nom de la Page</th>
                                    <th scope="col" style="width: 35%;">URL de Modification</th>
                                    <th scope="col" class="text-center" style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Définition locale des IDs des pages fixes. 
                                // Ces IDs DOIVENT être exclus de $pages_gauche et $pages_droite.
                                $PAGES_FIXES = [
                                    ['id' => 1, 'nom' => 'À propos', 'url_segment' => 'a-propos'],
                                    ['id' => 1, 'nom' => 'Status', 'url_segment' => 'status'],
                                    ['id' => 1, 'nom' => 'Présentation', 'url_segment' => 'presentation'],
                                    ['id' => 5, 'nom' => 'Photos / Vidéos', 'url_segment' => 'photos-videos'],
                                    ['id' => 12, 'nom' => 'Extra', 'url_segment' => 'extra'],
                                    ['id' => 13, 'nom' => 'Contacts', 'url_segment' => 'contacts'],
                                    ['id' => 14, 'nom' => 'Boutique', 'url_segment' => 'boutique'],
                                ];
                                $j = 1;
                                ?>

                                <?php foreach ($PAGES_FIXES as $page_fixe): ?>
                                    <?php if ($page_fixe['id'] == 5 || $page_fixe['id'] == 12 || $page_fixe['id'] == 13 || $page_fixe['id'] == 14): ?>
                                        <tr class="align-middle table-info">
                                            <th scope="row" class="text-muted"><?= $j++ ?></th>
                                            <td class="fw-medium">Page : <?= htmlspecialchars($page_fixe['nom']) ?> <span
                                                    class="badge bg-danger ms-2">Fixe</span></td>
                                            <td><code>/avva-admin/page/modifier/<?= $page_fixe['id'] ?></code>
                                            </td>

                                            <td class="text-center">
                                                <a href="/avva-admin/page/modifier/<?= $page_fixe['id'] ?>"
                                                    class="btn btn-sm btn-warning text-white rounded-pill"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Modifier le contenu de la page : <?= htmlspecialchars($page_fixe['nom']) ?>">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr class="align-middle table-info">
                                            <th scope="row" class="text-muted"><?= $j++ ?></th>
                                            <td class="fw-medium">Page : <?= htmlspecialchars($page_fixe['nom']) ?> <span
                                                    class="badge bg-danger ms-2">Fixe</span></td>
                                            <td><code>/avva-admin/page/<?= htmlspecialchars($page_fixe['url_segment']) ?>/<?= $page_fixe['id'] ?></code>
                                            </td>

                                            <td class="text-center">
                                                <a href="/avva-admin/page/<?= htmlspecialchars($page_fixe['url_segment']) ?>/<?= $page_fixe['id'] ?>"
                                                    class="btn btn-sm btn-warning text-white rounded-pill"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Modifier le contenu de la page : <?= htmlspecialchars($page_fixe['nom']) ?>">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (empty($PAGES_FIXES)): ?>
                                    <tr>
                                        <td colspan="5">
                                            <p class="text-center p-4 text-muted mb-0">Aucune page spéciale n'est définie.
                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // S'assure que le DOM est complètement chargé avant d'initialiser les tooltips
    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionne tous les éléments avec l'attribut data-bs-toggle="tooltip"
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

        // Initialise chaque tooltip
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>