<?php
// Hypothèse : $randonneeNom contient le nom de la randonnée (ex: 'Randonnée du Plateau')
// Si le nom n'est pas disponible, il faut le charger depuis l'entité si possible.
$randonneeNom = $afficherDetailsInscrits[0]->getCircuitRandonnee()->getNom();
?>

<div class="container my-5 content-section-page py-5">
    <div class="content">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-hiking me-2"></i> Groupe d'Inscription
                n°<?= htmlspecialchars($afficherDetailsInscritNumero) ?>
            </h1>
            <p class="lead text-light">Détails des participants pour la <?= htmlspecialchars($randonneeNom) ?></p>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur :</strong> <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Succès :</strong> <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!empty($afficherDetailsInscrits)): ?>
            <div class="row g-4">
                <?php $i = 1; ?>
                <?php foreach ($afficherDetailsInscrits as $afficherDetailsInscrit): ?>
                    <div class="col-lg-6">
                        <div class="card shadow-lg h-100 border-0 rounded-4 participant-card">
                            <div class="card-body p-4">
                                <h2 class="card-title h4 mb-3 d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="badge bg-primary rounded-pill me-2"><?= $i++ ?></span>
                                        <?= htmlspecialchars($afficherDetailsInscrit->getNom()) ?>
                                        <?= htmlspecialchars($afficherDetailsInscrit->getPrenom()) ?>
                                    </span>
                                    <?php if ($i === 2): // Assumons que le premier inscrit est le principal payeur ?>
                                        <span class="badge bg-info text-dark">Payeur Principal</span>
                                    <?php endif; ?>
                                </h2>
                                <hr class="my-3">

                                <div class="row details-list">
                                    <div class="col-sm-6 mb-2">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        Parcours :
                                        <span
                                            class="fw-bold text-success"><?= $afficherDetailsInscrit->getCircuitRandonnee()->getDistanceKm() ?>
                                            km</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <i class="fas fa-venus-mars text-muted me-2"></i>
                                        Sexe : <?= htmlspecialchars($afficherDetailsInscrit->getSexe()) ?>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <i class="fas fa-birthday-cake text-muted me-2"></i>
                                        Né(e) le : <?= $afficherDetailsInscrit->getDateNaissance()->format('d/m/Y') ?>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <i class="fas fa-phone text-muted me-2"></i>
                                        Téléphone : <?= htmlspecialchars($afficherDetailsInscrit->getNumTel()) ?>
                                    </div>
                                    <div class="col-sm-12 mb-2">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        Email : <?= htmlspecialchars($afficherDetailsInscrit->getEmail()) ?>
                                    </div>
                                </div>

                                <h5 class="mt-4 mb-2 text-primary"><i class="fas fa-user-tag me-2"></i> Licence</h5>
                                <ul class="list-unstyled licence-details">
                                    <li class="mb-1">
                                        Club FFVÉLO :
                                        <?php if ($afficherDetailsInscrit->getLicenceFfveloClub()): ?>
                                            <span
                                                class="badge bg-success"><?= htmlspecialchars($afficherDetailsInscrit->getLicenceFfveloClub()) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Non renseigné</span>
                                        <?php endif; ?>
                                    </li>
                                    <li class="mb-1">
                                        N° Licence :
                                        <?php if ($afficherDetailsInscrit->getNumLicence()): ?>
                                            <span
                                                class="badge bg-success"><?= htmlspecialchars($afficherDetailsInscrit->getNumLicence()) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Non licencié</span>
                                        <?php endif; ?>
                                    </li>
                                </ul>

                                <?php if ($afficherDetailsInscrit->getAdresse()): ?>
                                    <h5 class="mt-4 mb-2 text-primary"><i class="fas fa-home me-2"></i> Adresse</h5>
                                    <address class="mb-0">
                                        <?= htmlspecialchars($afficherDetailsInscrit->getAdresse()) ?><br>
                                        <?= htmlspecialchars($afficherDetailsInscrit->getCodePostal()) ?>
                                        <?= htmlspecialchars($afficherDetailsInscrit->getVille()) ?>
                                    </address>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center shadow-sm" role="alert">
                <i class="fas fa-info-circle me-2"></i> Aucun détail d'inscription trouvé pour ce numéro de groupe.
            </div>
        <?php endif; ?>
    </div>
</div>