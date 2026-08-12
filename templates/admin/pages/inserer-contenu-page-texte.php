<?php if ($page->getId() != 5 && $page->getId() != 8): ?>
    <div class="container">
        <div class="row flex">
            <div class="col py-3">
                <div class="mt-5 mb-5">
                    <h2 class="text-center">Insérer un texte dans la page : <?= $page->getNom() ?></h2>
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
                    <form id="editPageForm" method="post" class="card shadow-lg border-0 rounded-4 p-4 p-md-5 bg-white">
                        <h3
                            class="card-title mb-4 text-primary fw-bold <?= $page->getId() == 6 ? 'd-none' : 'd-block' ?>">
                            <i class="bi bi-file-earmark-text me-2"></i> Insertion d'un texte dans la page
                        </h3>

                        <div class="form-floating mb-3">
                            <input type="text" name="nom_contenu_page" id="nom_contenu_page"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                placeholder="Nom du contenu de la page" value="" required>
                            <label for="nom_contenu_page"><i class="bi bi-tag me-1"></i> Nom du contenu de la
                                page</label>
                        </div>

                        <div class="mb-3">
                            <label for="contenu_page" class="form-label fw-semibold text-muted">
                                <i class="bi bi-card-text me-1"></i> Contenu de la page
                            </label>
                            <textarea name="texte_contenu_page" id="contenu_page"
                                class="form-control <?= isset($error) ? 'border-danger' : '' ?>"
                                style="min-height: 200px;" required></textarea>
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
                </div>
            </div>
        </div>
    </div>
</div>