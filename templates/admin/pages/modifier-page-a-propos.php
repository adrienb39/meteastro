<div class="container">
    <div class="row flex">
        <div class="col py-3">
            <div class="mt-5 mb-5">
                <h2 class="text-center">Contenu de la page À propos</h2>
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
                    <form id="editPageForm" method="post" class="border shadow p-5 pe-5 rounded mx-auto">

                        <div class="mb-3">
                            <textarea type="text" name="contenu_page" id="contenu_page"
                                class="form-control <?= isset($error) ? 'border border-danger' : '' ?>"
                                required><?= htmlspecialchars($page->getContenu()) ?></textarea>
                        </div>

                        <!-- Affichage des erreurs -->
                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="form-text text-danger"><?= $error; ?></div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-primary" name="action"
                                value="save">Sauvegarder</button>
                            <a href="/avva-admin/page/apercu-a-propos/<?= $page->getId() ?>" target="_blank"
                                class="btn btn-info">Ouvrir la page complète</a>
                        </div>
                    </form>
                    <!-- Aperçu en temps réel -->
                    <div class="mt-4">
                        <iframe id="livePreview" class="rounded" style="width:100%; min-height:600px; border:1px solid #ccc;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>