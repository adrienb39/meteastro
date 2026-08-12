<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">

            <?php if (isset($_SESSION['isUserConnected']) && $_SESSION['isUserConnected']): ?>
                <div class="alert alert-success shadow-sm text-center p-4" role="alert">
                    <h4 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i> Vous êtes déjà connecté.</h4>
                    <p>Accédez à votre espace d'administration immédiatement.</p>
                    <hr>
                    <a href="/avva-admin/accueil" class="btn btn-success btn-lg">
                        <i class="bi bi-arrow-right-circle me-2"></i>
                        Espace membres
                    </a>
                </div>

            <?php elseif (isset($_SESSION['isUserConnectedLogin']) && $_SESSION['isUserConnectedLogin']): ?>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5">

                        <h2 class="card-title text-center mb-4 text-primary fw-bold">
                            <i class="bi bi-shield-lock me-2"></i>
                            Authentification
                        </h2>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-x-octagon-fill flex-shrink-0 me-2"></i>
                                <div><?= $_SESSION['error_message']; ?></div>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill flex-shrink-0 me-2"></i>
                                <div><?= $_SESSION['success_message']; ?></div>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <?php if (!isset($_SESSION['step']) || $_SESSION['step'] === 'email'): ?>
                            <p class="text-muted text-center mb-4">Étape 1/2 : Veuillez entrer votre identifiant.</p>
                            <form action="/avva-admin/login" method="POST">
                                <div class="mb-4">
                                    <label for="email_user" class="form-label fw-bold">Adresse e-mail</label>
                                    <input type="email" class="form-control form-control-lg" id="email_user" name="email_user"
                                        placeholder="Votre email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-arrow-right me-2"></i>
                                    Continuer
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['step']) && $_SESSION['step'] !== 'email'): ?>
                            <?php
                            $user_email = isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : '';
                            ?>
                            <div class="alert alert-secondary text-center small p-2 mb-4">
                                Connexion pour : <strong class="text-dark"><?= $user_email; ?></strong>
                            </div>

                            <form action="/avva-admin/login" method="POST">
                                <input type="hidden" name="email_user" value="<?= $user_email; ?>">

                                <?php if ($_SESSION['step'] === 'password'): ?>
                                    <h4 class="mb-3 text-center">Étape 2/2 : Mot de passe</h4>
                                    <div class="mb-4">
                                        <label for="password_user" class="form-label fw-bold">Mot de passe</label>
                                        <input type="password" class="form-control form-control-lg" id="password_user"
                                            name="password_user" placeholder="Votre mot de passe" required>
                                    </div>
                                    <div class="text-end mb-4">
                                        <a href="/avva-admin/mot-de-passe-oublie"
                                            class="small text-danger text-decoration-none fw-bold">
                                            Mot de passe oublié ?
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Se connecter
                                    </button>

                                <?php elseif ($_SESSION['step'] === 'temporary_code'): ?>
                                    <h4 class="mb-3 text-center">Étape 2/2 : Code temporaire</h4>
                                    <p class="text-center text-info small mb-4">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        Un code de connexion vous a été envoyé par email.
                                    </p>
                                    <div class="mb-4">
                                        <label for="code_user" class="form-label fw-bold">Code temporaire reçu</label>
                                        <input type="text" class="form-control form-control-lg" id="code_user" name="code_user"
                                            placeholder="Ex: 123456" required>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-lg w-100 text-white">
                                        <i class="bi bi-send-fill me-2"></i>
                                        Valider le code
                                    </button>

                                <?php elseif ($_SESSION['step'] === 'create_password'): ?>
                                    <h4 class="mb-3 text-center">Définir un mot de passe</h4>
                                    <p class="text-center text-warning small mb-4">
                                        <i class="bi bi-lock-fill me-1"></i>
                                        Ce compte n'a pas encore de mot de passe.
                                    </p>
                                    <div class="mb-3">
                                        <label for="password_user" class="form-label fw-bold">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="password_user" name="password_user"
                                            required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label fw-bold">Confirmer</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-lg w-100 text-white">
                                        <i class="bi bi-save-fill me-2"></i>
                                        Enregistrer
                                    </button>
                                <?php endif; ?>
                            </form>

                            <div class="text-center mt-4">
                                <a href="/avva-admin/login?reset=1" class="text-muted small">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Changer d'adresse e-mail
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="/" class="text-primary small text-decoration-none fw-bold">
                    <i class="bi bi-house-door-fill me-1"></i>
                    Retour au site principal
                </a>
            </div>

        </div>
    </div>
</div>