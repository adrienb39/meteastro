<div class="container py-5 mt-4">

    <!-- En-tête de la Page et Boutons d'Action -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="display-5 fw-bold text-success">
            <i class="fas fa-hiking me-2"></i>
            Inscriptions Pédestres
            <!-- Afficher le nom de la randonnée si disponible, ex: $randonneeCible->getTitre() -->
            <?php if (isset($randonneeCible)): ?>
                <small class="text-muted h4 d-block d-md-inline-block mt-2 mt-md-0">(<?= htmlspecialchars($randonneeCible->getTitre()) ?>)</small>
            <?php endif; ?>
        </h1>
        <div class="d-flex flex-column flex-md-row">
            <!-- Bouton d'Export CSV (Action administrative clé) -->
            <a class="btn btn-success d-flex align-items-center rounded-3 shadow-sm mb-2 mb-md-0 me-md-3"
                href="/avva-admin/export-pedestre/<?= $randonneeCible->getId() ?? '' ?>">
                <i class="fas fa-file-csv me-2"></i>
                Exporter la liste (.csv)
            </a>
        </div>
    </div>

    <!-- 1. Messages de Succès ou d'Erreur -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- 2. Tableau des Inscriptions -->
    <div class="card shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Nom & Prénom</th>
                            <th scope="col">Contact (Email / Tél)</th>
                            <th scope="col">Circuit</th>
                            <th scope="col" class="text-center">Paiement</th>
                            <th scope="col" class="text-center">Licence</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inscriptionsPedestre)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($inscriptionsPedestre as $inscription): ?>
                                <?php
                                    // Définition de la couleur du badge de paiement
                                    $statut = $inscription->getStatutPaiement();
                                    $badgeClass = 'bg-secondary';
                                    $statutText = 'Inconnu';

                                    switch ($statut) {
                                        case 'PAYÉ':
                                            $badgeClass = 'bg-success';
                                            $statutText = 'Payé';
                                            break;
                                        case 'ATTENTE_PAIEMENT':
                                            $badgeClass = 'bg-warning text-dark';
                                            $statutText = 'En Attente';
                                            break;
                                        case 'ANNULÉ':
                                            $badgeClass = 'bg-danger';
                                            $statutText = 'Annulé';
                                            break;
                                        case 'NON_APPLICABLE':
                                            $badgeClass = 'bg-info';
                                            $statutText = 'Non Applicable';
                                            break;
                                    }

                                    // Vérifie si l'adresse est remplie (indique souvent le payeur principal)
                                    $isPayer = !empty($inscription->getAdresse());
                                ?>
                                <tr <?= $isPayer ? 'class="table-light"' : '' ?>>
                                    <th scope="row" class="text-center"><?= $i++ ?></th>
                                    <td>
                                        <?= htmlspecialchars($inscription->getNom() . ' ' . $inscription->getPrenom()) ?>
                                        <?php if ($isPayer): ?>
                                            <span class="badge bg-primary ms-2" title="Inscrit principal/Payeur"><i class="fas fa-hand-holding-usd"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope me-1 text-muted"></i> <?= htmlspecialchars($inscription->getEmail()) ?><br>
                                        <i class="fas fa-phone me-1 text-muted"></i> <?= htmlspecialchars($inscription->getNumTel()) ?>
                                    </td>
                                    <td>
                                        <!-- Suppose que getCircuitRandonnee() a une méthode getNomCircuit() ou getTitre() -->
                                        <?= htmlspecialchars($inscription->getCircuitRandonnee()->getNom()) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?>"><?= $statutText ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($inscription->getNumLicence()): ?>
                                            <i class="fas fa-check-circle text-success" title="Licence FFVelo ou autre fournie"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-danger" title="Pas de licence"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <!-- Bouton Voir Détails (Ouvre un modal) -->
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $inscription->getId() ?>" title="Voir les détails complets">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        
                                        <!-- Bouton Modifier Statut Paiement (Si nécessaire) -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#paiementModal<?= $inscription->getId() ?>" title="Modifier le statut de paiement">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center p-4 text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Aucune inscription pédestre trouvée pour cette randonnée.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals de Détails et Paiement (Exemple de structure) -->
<?php if (!empty($inscriptionsPedestre)): ?>
    <?php foreach ($inscriptionsPedestre as $inscription): 
        $id = $inscription->getId();
    ?>
    
    <!-- Modal de Détails Complets -->
    <div class="modal fade" id="detailModal<?= $id ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $id ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="detailModalLabel<?= $id ?>">Détails de l'Inscrit : <?= htmlspecialchars($inscription->getPrenom() . ' ' . $inscription->getNom()) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Informations Personnelles</h6>
                    <div class="row">
                        <div class="col-md-6"><p><strong>N° Dossier:</strong> <?= htmlspecialchars($inscription->getNumeroInscription()) ?></p></div>
                        <div class="col-md-6"><p><strong>Sexe:</strong> <?= htmlspecialchars($inscription->getSexe()) ?></p></div>
                        <div class="col-md-6"><p><strong>Date de Naissance:</strong> <?= htmlspecialchars($inscription->getDateNaissance()->format('d/m/Y')) ?></p></div>
                        <div class="col-md-6"><p><strong>Contact d'Urgence:</strong> <?= htmlspecialchars($inscription->getNomPrenomTel()) ?></p></div>
                    </div>
                    
                    <h6 class="mt-3">Adresse (Si Payeur)</h6>
                    <p>
                        <?= htmlspecialchars($inscription->getAdresse() ?? 'N/A') ?><br>
                        <?= htmlspecialchars($inscription->getCodePostal() ?? '') ?> <?= htmlspecialchars($inscription->getVille() ?? '') ?>
                    </p>

                    <h6 class="mt-3">Licence & Club</h6>
                    <p>
                        <strong>FFVelo/Club:</strong> <?= htmlspecialchars($inscription->getLicenceFfveloClub() ?? 'Non renseigné') ?><br>
                        <strong>N° Licence:</strong> <?= htmlspecialchars($inscription->getNumLicence() ?? 'N/A') ?><br>
                        <strong>Autre Fédération/Club:</strong> <?= htmlspecialchars($inscription->getAutreFederationClub() ?? 'N/A') ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Modification de Paiement -->
    <div class="modal fade" id="paiementModal<?= $id ?>" tabindex="-1" aria-labelledby="paiementModalLabel<?= $id ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="paiementModalLabel<?= $id ?>">Modifier Statut de Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/avva-admin/modifier-statut-paiement/<?= $id ?>" method="POST">
                    <div class="modal-body">
                        <p>Statut actuel pour **<?= htmlspecialchars($inscription->getPrenom() . ' ' . $inscription->getNom()) ?>** : <span class="badge <?= $badgeClass ?>"><?= $statutText ?></span></p>
                        
                        <label for="new_statut_<?= $id ?>" class="form-label fw-bold">Nouveau Statut :</label>
                        <select class="form-select" id="new_statut_<?= $id ?>" name="statut" required>
                            <option value="ATTENTE_PAIEMENT" <?= $statut == 'ATTENTE_PAIEMENT' ? 'selected' : '' ?>>En Attente</option>
                            <option value="PAYE" <?= $statut == 'PAYÉ' ? 'selected' : '' ?>>Payé</option>
                            <option value="ANNULE" <?= $statut == 'ANNULÉ' ? 'selected' : '' ?>>Annulé</option>
                            <option value="NON_APPLICABLE" <?= $statut == 'NON_APPLICABLE' ? 'selected' : '' ?>>Non Applicable (Ex: Gratuit/Organisateur)</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer les Modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>
<?php endif; ?>