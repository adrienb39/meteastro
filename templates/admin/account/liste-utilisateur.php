<div class="container mt-4 rounded-3">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1>Liste des utilisateurs</h1>
        <a class="btn btn-dark d-flex align-items-center rounded-3" style="text-decoration: none;"
            href="/avva-admin/creer-utilisateur">Ajouter un utilisateur</a>
    </div>

    <!-- Message de succès ou d'erreur -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <div class="card shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 rounded-4 border">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nom de l'utilisateur</th>
                            <th scope="col">Prénom de l'utilisateur</th>
                            <th scope="col">Email de l'utilisateur</th>
                            <th scope="col">Rôle de l'utilisateur</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($usersAdmin as $userAdmin): ?>
                                <tr>
                                    <th scope="row"><?= $i++ ?></th>
                                    <td><?= $userAdmin->getNom() ?></td>
                                    <td><?= $userAdmin->getPrenom() ?></td>
                                    <td><?= $userAdmin->getEmail() ?></td>
                                    <td><?= $userAdmin->getRole()->getNom() ?></td>
                                    <?php if ($userAdmin->getId() != $_SESSION['user']['id'] && $userAdmin->getId() != 1): ?>
                                    <td class="d-flex justify-content-evenly">
                                        <a style="text-decoration: none; color: black;"
                                            href="/avva-admin/modifier-utilisateur/<?= $userAdmin->getId() ?>"><img
                                                style="width: 16px;" src="/assets/images/pen-to-square-solid.svg"
                                                alt="modify"></a>
                                        <!-- <a style="text-decoration: none; color: black;"
                            href="/admin/pages/supprimer-section/<?= $userAdmin->getId() ?>"><img
                                style="width: 16px;" src="/assets/images/x-circle-fill.svg" alt="delete"></a> -->
                                    </td>
                                    <?php endif; ?>
                                </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>