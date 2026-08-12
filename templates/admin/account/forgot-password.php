<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <h2 class="card-title text-center mb-4 text-warning fw-bold">
                        <i class="bi bi-question-circle me-2"></i>
                        Mot de passe oublié
                    </h2>
                    
                    <p class="text-center text-muted mb-4">
                        Veuillez entrer votre adresse e-mail. Nous vous enverrons un code pour créer un nouveau mot de passe.
                    </p>

                    <?php session_start(); ?>
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

                    <form action="/avva-admin/mot-de-passe-oublie" method="POST">
                        <div class="mb-4">
                            <label for="email_user" class="form-label fw-bold">Adresse e-mail</label>
                            <input type="email" class="form-control form-control-lg" id="email_user" name="email_user" placeholder="Votre email" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg w-100 text-white">
                            <i class="bi bi-envelope-fill me-2"></i>
                            Envoyer le code
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="/avva-admin/login" class="text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>
                            Retour à la connexion
                        </a>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</div>