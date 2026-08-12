<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="p-4 p-md-5 border shadow rounded bg-white mt-5">
                <h2 class="text-center mb-4">Créer un compte utilisateur</h2>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="nom_utilisateur" class="form-label">Nom de l'utilisateur</label>
                        <input type="text" name="nom_utilisateur" id="nom_utilisateur"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="prenom_utilisateur" class="form-label">Prénom de l'utilisateur</label>
                        <input type="text" name="prenom_utilisateur" id="prenom_utilisateur"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email_utilisateur" class="form-label">Email de l'utilisateur</label>
                        <input type="email" name="email_utilisateur" id="email_utilisateur"
                            class="form-control <?= isset($error) ? 'is-invalid' : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="role_utilisateur" class="form-label">Rôle</label>
                        <select class="form-select <?= isset($error) ? 'is-invalid' : '' ?>" id="role_utilisateur"
                            name="role_utilisateur" required>
                            <option value="">-- Sélectionner un rôle --</option>
                            <?php
                            // Affichage des rôles
                            foreach ($roles as $role) {
                                echo "<option value=\"{$role->getId()}\">{$role->getNom()}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <?php if (isset($error) && $error != ""): ?>
                        <div class="alert alert-danger mt-3" role="alert"><?= $error; ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        Créer le compte utilisateur
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>