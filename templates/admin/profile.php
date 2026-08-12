<?php
// Récupération des messages de session (comme dans settings.php)
session_start();
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<div class="container-fluid py-5" style="margin-top: 50px;">
    <div class="row">
        <div class="col-12">
            <h1 class="display-5 fw-bold mb-4 text-primary">
                <span class="material-symbols-rounded me-3" style="font-size: 1em;">manage_accounts</span>
                Modifier mon Profil
            </h1>
            <p class="lead text-muted">Mettez à jour vos informations personnelles et votre mot de passe.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-lg-8">
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

            <form action="/avva-admin/profile/save" method="POST" class="card border-0 shadow-lg p-4">
                <div class="card-body">

                    <h2 class="h4 mb-4 border-bottom pb-2 text-primary">
                        <i class="fas fa-user-circle me-2"></i> Détails Personnels
                    </h2>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label fw-semibold">Prénom <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom"
                                value="<?= htmlspecialchars($userEntity->getPrenom()); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label fw-semibold">Nom <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                value="<?= htmlspecialchars($userEntity->getNom()); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Adresse E-mail <span
                                class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($userEntity->getEmail()); ?>" required>
                        <div class="form-text">Ceci est l'identifiant que vous utilisez pour vous connecter.</div>
                    </div>

                    <hr class="my-5">

                    <h2 class="h4 mb-4 border-bottom pb-2 text-warning">
                        <i class="fas fa-lock me-2"></i> Changement de Mot de Passe
                    </h2>
                    <div class="alert alert-info small">
                        Ne remplissez ces champs que si vous souhaitez changer votre mot de passe.
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                        <div class="form-text">Obligatoire pour confirmer toute modification de mot de passe.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_password_confirm" class="form-label fw-semibold">Confirmer le nouveau mot de
                                passe</label>
                            <input type="password" class="form-control" id="new_password_confirm"
                                name="new_password_confirm">
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i> Enregistrer les Modifications
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>