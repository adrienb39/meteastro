<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="p-4 p-md-5 border shadow rounded bg-white mt-5">
                <h2 class="text-center mb-4">Modifier le compte utilisateur
                    <?= htmlspecialchars($userAdmin->getNom()) ?>
                    <?= htmlspecialchars($userAdmin->getPrenom()) ?>
                </h2>

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
                        <label class="form-label fw-bold">Nom de l'utilisateur :</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($userAdmin->getNom()) ?></p>
                        <input type="hidden" name="nom_utilisateur"
                            value="<?= htmlspecialchars($userAdmin->getNom()) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Prénom de l'utilisateur :</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($userAdmin->getPrenom()) ?></p>
                        <input type="hidden" name="prenom_utilisateur"
                            value="<?= htmlspecialchars($userAdmin->getPrenom()) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de l'utilisateur :</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($userAdmin->getEmail()) ?></p>
                        <input type="hidden" name="email_utilisateur"
                            value="<?= htmlspecialchars($userAdmin->getEmail()) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="role_utilisateur" class="form-label">Rôle</label>
                        <select class="form-select <?= isset($error) ? 'is-invalid' : '' ?>" id="role_utilisateur"
                            name="role_utilisateur" required>
                            <option value="">-- Sélectionner un rôle --</option>
                            <?php
                            $currentRoleId = $userAdmin->getRole() ? $userAdmin->getRole()->getId() : null;
                            foreach ($roles as $role) {
                                $selected = ($role->getId() == $currentRoleId) ? 'selected' : '';
                                echo "<option value=\"{$role->getId()}\" {$selected}>{$role->getNom()}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <?php if (isset($error) && $error != ""): ?>
                        <div class="alert alert-danger mt-3" role="alert"><?= $error; ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        Modifier le compte utilisateur
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>