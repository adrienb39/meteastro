<?php if (isset($_SESSION['isUserConnected']) && $_SESSION['isUserConnected']): ?>
    <?php
    // Gestion des messages de session
    $success_message = $_SESSION['success_message'] ?? null;
    $error_message = $_SESSION['error_message'] ?? null;
    unset($_SESSION['success_message'], $_SESSION['error_message']);

    // Définir le chemin d'accès au logo pour l'affichage
    // Assurez-vous que 'logo_filename' est bien présent dans votre tableau $settings
    $logoFilename = $settings['logo_filename'] ?? null;
    $logoPath = $logoFilename ? '/uploads/logo/' . $logoFilename : null;

    $imageFondFilename = $settings['image_fond_filename'] ?? null;
    $imageFondPath = $imageFondFilename ? '/uploads/image-fond/' . $imageFondFilename : null;
    ?>

    <div class="container-fluid py-5" style="margin-top: 50px;">

        <div class="row mt-4">
            <div class="col-12 col-lg-10 mx-auto">

                <h1 class="display-5 fw-bold mb-4 text-primary">
                    <i class="fas fa-cog me-3"></i>
                    Paramètres du Site
                </h1>
                <p class="lead text-muted">Gérez les configurations globales et l'identité visuelle de votre site.</p>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="/avva-admin/settings/save" method="POST" class="card border-0 shadow-lg p-4 mt-4"
                    id="settingsForm" enctype="multipart/form-data">
                    <div class="card-body">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-primary"><i class="fas fa-cogs me-2"></i> Configuration
                            Générale</h2>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="site_name" class="form-label fw-semibold">Nom du Site / Titre <span
                                        class="text-danger">*</span></label>
                                <input type="hidden" id="site_name" name="site_name"
                                    value="<?= htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                                <p class="form-control-plaintext"><?= htmlspecialchars($settings['site_name'] ?? ''); ?></p>
                                <div class="form-text">Ce titre apparaît dans la barre du navigateur.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="image_fond_file" class="form-label fw-semibold">Téléverser l'image de fond
                                    (.png, .jpg, .svg)</label>
                                <input class="form-control" type="file" id="image_fond_file" name="image_fond_file"
                                    accept=".jpg, .jpeg, .png, .svg">
                                <div class="form-text">Taille maximale recommandée : 500x500 pixels.</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Image de fond Actuel</label>
                                <div class="p-2 border rounded d-flex align-items-center justify-content-center"
                                    style="height: 100px;">
                                    <?php if ($imageFondPath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageFondPath)): ?>
                                        <img src="<?= htmlspecialchars($imageFondPath); ?>" alt="Image de fond Actuel"
                                            class="img-fluid" style="max-height: 80px;">
                                    <?php else: ?>
                                        <span class="text-muted small">Aucune image de fond défini.</span>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="current_image_fond_filename"
                                    value="<?= htmlspecialchars($imageFondFilename ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="logo_file" class="form-label fw-semibold">Téléverser le Logo (.png, .jpg,
                                    .svg)</label>
                                <input class="form-control" type="file" id="logo_file" name="logo_file"
                                    accept=".jpg, .jpeg, .png, .svg">
                                <div class="form-text">Taille maximale recommandée : 500x500 pixels.</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Logo Actuel</label>
                                <div class="p-2 border rounded d-flex align-items-center justify-content-center"
                                    style="height: 100px;">
                                    <?php if ($logoPath && file_exists($_SERVER['DOCUMENT_ROOT'] . $logoPath)): ?>
                                        <img src="<?= htmlspecialchars($logoPath); ?>" alt="Logo Actuel" class="img-fluid"
                                            style="max-height: 80px;">
                                    <?php else: ?>
                                        <span class="text-muted small">Aucun logo défini.</span>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="current_logo_filename"
                                    value="<?= htmlspecialchars($logoFilename ?? ''); ?>">
                            </div>
                        </div>

                        <hr class="my-5">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-primary"><i class="fas fa-user-circle me-2"></i>
                            Coordonnées du Président</h2>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="president_nom" class="form-label fw-semibold">Nom du Président <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="president_nom" name="president_nom"
                                    value="<?= htmlspecialchars($settings['president_nom'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label fw-semibold">Téléphone de Contact</label>
                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone"
                                    value="<?= htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <h5 class="h6 mt-3 mb-2 text-muted">Adresse du Président</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="president_adresse_rue" class="form-label">Rue</label>
                                <input type="text" class="form-control" id="president_adresse_rue"
                                    name="president_adresse_rue"
                                    value="<?= htmlspecialchars($settings['president_adresse_rue'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="president_adresse_cp_ville" class="form-label">CP & Ville</label>
                                <input type="text" class="form-control" id="president_adresse_cp_ville"
                                    name="president_adresse_cp_ville"
                                    value="<?= htmlspecialchars($settings['president_adresse_cp_ville'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_email" class="form-label fw-semibold">Adresse E-mail de Contact <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email"
                                value="<?= htmlspecialchars($settings['contact_email'] ?? ''); ?>" required>
                            <div class="form-text">Cet e-mail est affiché dans le pied de page et utilisé pour les
                                formulaires.</div>
                        </div>

                        <hr class="my-5">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-primary"><i class="fas fa-handshake me-2"></i> Partenaire
                            Principal (Ex: Jura Cycles)</h2>
                        <div class="alert alert-info small">
                            Ces informations sont affichées dans la première section du pied de page.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="partenaire_1_nom" class="form-label">Nom du Partenaire</label>
                                <input type="text" class="form-control" id="partenaire_1_nom" name="partenaire_1_nom"
                                    value="<?= htmlspecialchars($settings['partenaire_1_nom'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="partenaire_1_url" class="form-label">URL du Site (Ex: https://...)</label>
                                <input type="url" class="form-control" id="partenaire_1_url" name="partenaire_1_url"
                                    value="<?= htmlspecialchars($settings['partenaire_1_url'] ?? ''); ?>">
                            </div>
                        </div>

                        <h5 class="h6 mt-3 mb-2 text-muted">Adresse du du partenaire</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="partenaire_1_adresse_rue" class="form-label">Rue</label>
                                <input type="text" class="form-control" id="partenaire_1_adresse_rue"
                                    name="partenaire_1_adresse_rue"
                                    value="<?= htmlspecialchars($settings['partenaire_1_adresse_rue'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="partenaire_1_adresse_cp_ville" class="form-label">CP & Ville</label>
                                <input type="text" class="form-control" id="partenaire_1_adresse_cp_ville"
                                    name="partenaire_1_adresse_cp_ville"
                                    value="<?= htmlspecialchars($settings['partenaire_1_adresse_cp_ville'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="partenaire_1_tel" class="form-label">Téléphone du Partenaire</label>
                            <input type="tel" class="form-control" id="partenaire_1_tel" name="partenaire_1_tel"
                                value="<?= htmlspecialchars($settings['partenaire_1_tel'] ?? ''); ?>">
                        </div>

                        <hr class="my-5">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-primary"><i class="fas fa-link me-2"></i> Liens Externes
                        </h2>

                        <h5 class="h6 mt-3 mb-2 text-muted">Réseaux Sociaux</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="social_facebook_url" class="form-label"><i class="fab fa-facebook me-1"></i> URL
                                    Facebook</label>
                                <input type="url" class="form-control" id="social_facebook_url" name="social_facebook_url"
                                    value="<?= htmlspecialchars($settings['social_facebook_url'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="social_youtube_url" class="form-label"><i class="fab fa-youtube me-1"></i> URL
                                    Youtube</label>
                                <input type="url" class="form-control" id="social_youtube_url" name="social_youtube_url"
                                    value="<?= htmlspecialchars($settings['social_youtube_url'] ?? ''); ?>">
                            </div>
                        </div>

                        <h5 class="h6 mt-3 mb-2 text-muted">Liens Fédérations / CODEP</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ffvelo_url" class="form-label">URL FFVÉLO</label>
                                <input type="url" class="form-control" id="ffvelo_url" name="ffvelo_url"
                                    value="<?= htmlspecialchars($settings['ffvelo_url'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="codep39_url" class="form-label">URL CODEP 39</label>
                                <input type="url" class="form-control" id="codep39_url" name="codep39_url"
                                    value="<?= htmlspecialchars($settings['codep39_url'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr class="my-5">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-primary">
                            <i class="fas fa-palette me-2"></i> Couleurs du Thème
                        </h2>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="theme_text_color" class="form-label fw-semibold">Couleur du texte</label>
                                <input type="color" class="form-control form-control-color" id="theme_text_color"
                                    name="theme_text_color"
                                    value="<?= htmlspecialchars($settings['theme_text_color'] ?? '#000000'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="theme_fond_color" class="form-label fw-semibold">Couleur du fond</label>
                                <input type="color" class="form-control form-control-color" id="theme_fond_color"
                                    name="theme_fond_color"
                                    value="<?= htmlspecialchars($settings['theme_fond_color'] ?? '#ffffff'); ?>">
                            </div>
                        </div>

                        <hr class="my-5">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-primary">
                            <i class="fas fa-image me-2"></i> Fond des pages
                        </h2>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="page_fond_color" class="form-label fw-semibold">Couleur du fond</label>
                                <input type="color" class="form-control form-control-color" id="page_fond_color"
                                    name="page_fond_color"
                                    value="<?= htmlspecialchars($settings['page_fond_color'] ?? '#ffffff'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="page_fond_transparent" class="form-label fw-semibold">Transparence du
                                    fond</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="page_fond_transparent"
                                        name="page_fond_transparent" value="1" <?= ($settings['page_fond_transparent'] ?? 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="page_fond_transparent">Rendre le fond
                                        transparent</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5">

                        <h2 class="h4 mb-4 border-bottom pb-2 text-danger"><i class="fas fa-check-square me-2"></i>
                            Confirmation Obligatoire</h2>

                        <div class="card p-3 border-danger bg-light mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="confirmationCheck"
                                    name="confirmation_check" required>
                                <label class="form-check-label fw-bold text-danger" for="confirmationCheck">
                                    Je confirme avoir vérifié l'exactitude de toutes les informations ci-dessus.
                                </label>
                                <div class="form-text">Attention : La modification de ces paramètres affecte l'affichage
                                    public du site.</div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitButton">
                                <i class="fas fa-save me-2"></i> Enregistrer les Paramètres
                            </button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const confirmationCheckbox = document.getElementById('confirmationCheck');
                                const submitButton = document.getElementById('submitButton');

                                if (!confirmationCheckbox || !submitButton) {
                                    console.error("Erreur JS: Éléments manquants.");
                                    return;
                                }

                                function toggleSubmitButton() {
                                    if (confirmationCheckbox.checked) {
                                        submitButton.removeAttribute('disabled');
                                        submitButton.classList.remove('disabled');
                                    } else {
                                        submitButton.setAttribute('disabled', 'disabled');
                                        submitButton.classList.add('disabled');
                                    }
                                }

                                // État initial: Désactivé
                                toggleSubmitButton();

                                // Écouteur
                                confirmationCheckbox.addEventListener('change', toggleSubmitButton);
                            });
                        </script>

                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>