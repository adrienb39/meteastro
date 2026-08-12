<?php
// Assurez-vous que les variables PHP sont définies dans le contrôleur avant d'inclure ce template.

// --- 1. SÉCURITÉ PHP DE DÉPART (Redondance utile si le contrôleur n'a pas fait la redirection) ---
if (!isset($_SESSION['isUserConnected']) || !$_SESSION['isUserConnected']):
    // Dans une application MVC, ceci est généralement géré par le contrôleur qui fait une redirection
    // mais pour la robustesse du template:
    ?>
    <div class="alert alert-danger">Accès refusé. Veuillez vous connecter.</div>
<?php else: // Si l'utilisateur est bien connecté ?>

    <div class="container-fluid py-5" style="margin-top: 50px;">
        <div class="row">
            <div class="col-12">
                <h1 class="display-5 fw-bold mb-4 text-primary">
                    <i class="fas fa-tachometer-alt me-3"></i>
                    Tableau de Bord AVVA39
                </h1>
                <p class="lead">
                    Bienvenue, <?= htmlspecialchars($_SESSION['user']['email'] ?? 'Utilisateur'); ?> !
                </p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($_SESSION['error_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div
                        class="card-body d-flex flex-column flex-md-row justify-content-md-between align-items-center bg-light rounded-3">
                        <p class="mb-0 text-muted">
                            <i class="fas fa-user-circle me-2"></i>
                            Connecté en tant que <?= htmlspecialchars($_SESSION['user']['email'] ?? 'Administrateur'); ?>
                        </p>
                        <a href="/avva-admin/logout" class="btn btn-danger btn-sm mt-2 mt-md-0">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Se déconnecter
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h2 class="h4 mb-4 mt-3">📈 Statistiques Générales</h2>
        <div class="row g-4 mb-5">

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-eye fa-3x opacity-75 me-3"></i>
                            <div>
                                <p class="card-text text-uppercase fw-semibold mb-1" style="color: inherit !important;">
                                    Visites Totales</p>
                                <h3 class="card-title fw-bold" style="color: inherit !important;"><?= $nombreVisite ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-eye fa-3x opacity-75 me-3"></i>
                            <div>
                                <p class="card-text text-uppercase fw-semibold mb-1" style="color: inherit !important;">
                                    Visites Uniques (Mois)</p>
                                <h3 class="card-title fw-bold" style="color: inherit !important;">
                                    <?= $nombreVisiteParMois ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt fa-3x opacity-75 me-3"></i>
                            <div>
                                <p class="card-text text-uppercase fw-semibold mb-1" style="color: inherit !important;">
                                    Pages Totales</p>
                                <h3 class="card-title fw-bold" style="color: inherit !important;"><?= $nombrePages ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-comments fa-3x opacity-75 me-3"></i>
                            <div>
                                <p class="card-text text-uppercase fw-semibold mb-1" style="color: inherit !important;">Commentaires en Attente</p>
                                <h3 class="card-title fw-bold" style="color: inherit !important;">5</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <hr>

        <h2 class="h4 mb-4 mt-3">🚴 Détails de la Randonnée</h2>
        <div class="row g-4 mb-5">
            <?php if (!empty($prochaineRandonnee)):
                // Calcul du pourcentage de remplissage
                $max = $prochaineRandonnee->getNombreParticipantsMax() ?? 1; // Évite la division par zéro
                $pourcentage = ($max > 0) ? round(($nombreTotalInscrits ?? 0 / $max) * 100) : 0;
                $progress_color = ($pourcentage >= 90) ? 'bg-danger' : (($pourcentage >= 50) ? 'bg-warning' : 'bg-success');
                ?>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-header bg-info text-white fw-bold h5">
                            <i class="fas fa-calendar-check me-2"></i> Randonnée :
                            <?= htmlspecialchars($prochaineRandonnee->getTitre()) ?>
                            <span
                                class="badge bg-light text-dark float-end"><?= $prochaineRandonnee->getDateRandonnee()->format('d/m/Y') ?></span>
                        </div>
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted mb-0">Total Inscriptions Confirmées</h6>
                            <h4 class="fw-bold text-info mb-3"><?= $nombreTotalInscrits ?>
                                <?php if ($max): ?>
                                    / <?= $max ?><?php endif; ?><?= $nombreInscrits > 1 ? 'participants' : 'participant' ?>
                            </h4>

                            <?php if ($max): ?>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar <?= $progress_color ?>" role="progressbar"
                                        style="width: <?= $pourcentage ?>%" aria-valuenow="<?= $nombreTotalInscrits ?>"
                                        aria-valuemin="0" aria-valuemax="<?= $max ?>">
                                        <?= $pourcentage ?>% Rempli
                                    </div>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted d-block text-end">
                                Statut : <?= $prochaineRandonnee->getStatutInscription() ?>
                                <br>
                                <?php if ($max): ?>
                                    Statut : <?= $nombreTotalInscrits < $max ? 'Inscriptions ouvertes' : 'Complet' ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-header bg-secondary text-white fw-bold h5">
                            <i class="fas fa-chart-pie me-2"></i> Répartition des Inscriptions par Circuit
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush rounded-3">

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a class="text-decoration-none text-dark fw-medium"
                                        href="/avva-admin/liste-inscrits-randonnees-vtt/<?= $prochaineRandonnee->getId() ?>">
                                        <i class="fas fa-mountain me-2"></i> VTT
                                    </a>
                                    <span class="badge bg-primary rounded-pill"><?= $nombreInscritsVTT ?? 0 ?></span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a class="text-decoration-none text-dark fw-medium"
                                        href="/avva-admin/liste-inscrits-randonnees-gravel/<?= $prochaineRandonnee->getId() ?>">
                                        <i class="fas fa-road me-2"></i> Gravel
                                    </a>
                                    <span class="badge bg-primary rounded-pill"><?= $nombreInscritsGravel ?? 0 ?></span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a class="text-decoration-none text-dark fw-medium"
                                        href="/avva-admin/liste-inscrits-randonnees-route/<?= $prochaineRandonnee->getId() ?>">
                                        <i class="fas fa-biking me-2"></i> Route
                                    </a>
                                    <span class="badge bg-primary rounded-pill"><?= $nombreInscritsRoute ?? 0 ?></span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a class="text-decoration-none text-dark fw-medium"
                                        href="/avva-admin/liste-inscrits-randonnees-pedestre/<?= $prochaineRandonnee->getId() ?>">
                                        <i class="fas fa-walking me-2"></i> Pédestre
                                    </a>
                                    <span class="badge bg-primary rounded-pill"><?= $nombreInscritsPedestre ?? 0 ?></span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light fw-bold">
                                    Total Général (Toutes catégories)
                                    <span class="badge bg-secondary rounded-pill"><?= $nombreTotalInscrits ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-secondary">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucune randonnée (prochaine ou dernière publiée) trouvée pour afficher les statistiques
                        d'inscription.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <hr>

        <h2 class="h4 mb-4 mt-3">🛠️ Actions Rapides</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="fas fa-file-invoice me-2"></i> Gérer les Randonnées
                        </h5>
                        <p class="card-text text-muted">Créer, modifier ou publier un événement de randonnée.</p>
                        <a href="/avva-admin/randonnee/liste" class="btn btn-outline-primary btn-sm">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="fas fa-users-cog me-2"></i> Gérer les Utilisateurs
                        </h5>
                        <p class="card-text text-muted">Modifier les rôles ou ajouter des utilisateurs admins.</p>
                        <a href="/avva-admin/liste-utilisateur" class="btn btn-outline-success btn-sm">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-secondary"><i class="fas fa-list-alt me-2"></i> Lister les Pages</h5>
                        <p class="card-text text-muted">Affichez ou modifiez les pages statiques de votre site.</p>
                        <a href="/avva-admin/page/liste" class="btn btn-outline-secondary btn-sm">Accéder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>