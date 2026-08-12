<div class="container">
    <div class="row flex">
        <div class="col py-3">
            <div class="mt-5 mb-5">
                <h2 class="text-center">Créer une page</h2>
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
                    <form method="post" class="border shadow p-5 pe-5 rounded mx-auto">
                        <!-- Titre du Challenge -->
                        <div class="mb-3">
                            <label for="nom_page" class="form-label">Nom de la page</label>
                            <input type="text" name="nom_page" id="nom_page"
                                class="form-control <?= isset($error) ? 'border border-danger' : '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="url_page" class="form-label">Url de la page</label>
                            <input type="text" name="url_page" id="url_page"
                                class="form-control <?= isset($error) ? 'border border-danger' : '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="contenu_page" class="form-label">Contenu de la page</label>
                            <textarea type="text" name="contenu_page" id="contenu_page"
                                class="form-control <?= isset($error) ? 'border border-danger' : '' ?>"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="disposition_page_accueil" class="form-label">Disposition de la page</label>
                            <select class="form-select form-control" id="club" name="disposition_page_accueil" required>
                                <option value="">-- Sélectionner une disposition --</option>
                                <?php
                                // Affichage des clubs
                                foreach ($dispositionsAccueil as $dispositionAccueil) {
                                    echo "<option value=\"{$dispositionAccueil->getId()}\">{$dispositionAccueil->getNom()}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Affichage des erreurs -->
                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="form-text text-danger"><?= $error; ?></div>
                        <?php endif; ?>

                        <button type="submit" class="btn mt-2 btn-primary">Sauvegarder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>