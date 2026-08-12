<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="card border-0 shadow-lg rounded-4 my-5 p-4 p-md-5 bg-light">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4 text-primary fw-bolder">
                        <i class="bi bi-person-fill-gear me-2"></i> Modifier le Membre
                    </h2>

                    <?php 
                        $nomComplet = trim(($membre->getPrenom() ?? '') . ' ' . ($membre->getNom() ?? ''));
                        $dateNaissanceStr = $membre->getDateNaissance() ? $membre->getDateNaissance()->format('d/m/Y') : 'Non renseignée';
                        $licenceStr = $membre->getNumeroLicence() ? 'N° ' . $membre->getNumeroLicence() : 'Sans licence';
                        $sexeStr = $membre->getSexe() ? $membre->getSexe() : 'N/A';
                    ?>

                    <div class="bg-white p-3 rounded-3 mb-4 shadow-sm border">
                        <h4 class="text-center mb-1 text-dark fw-bold">
                            <?= !empty($nomComplet) ? htmlspecialchars($nomComplet) : htmlspecialchars($membre->getEmail()) ?>
                        </h4>
                        <p class="text-center text-muted mb-0 small">
                            Licence: <?= htmlspecialchars($licenceStr) ?>
                            • Né(e) le: <?= htmlspecialchars($dateNaissanceStr) ?>
                            • Sexe: <?= htmlspecialchars($sexeStr) ?>
                        </p>
                        <?php if ($membre->getPlan()): ?>
                            <div class="text-center mt-2">
                                <span class="badge bg-secondary"><?= htmlspecialchars(strtoupper($membre->getPlan())) ?></span>
                                <?php if ($membre->getPlan() === 'trial' && $membre->getDateFinEssai()): ?>
                                    <span class="small text-muted ms-1">(Fin d'essai : <?= htmlspecialchars($membre->getDateFinEssai()->format('d/m/Y')) ?>)</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            <?= htmlspecialchars($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= htmlspecialchars($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <form method="post" class="mt-4">
                        <input type="hidden" name="numero_licence_membre" value="<?= htmlspecialchars($membre->getNumeroLicence() ?? '') ?>">
                        <input type="hidden" name="nom_membre" value="<?= htmlspecialchars($membre->getNom() ?? '') ?>">
                        <input type="hidden" name="prenom_membre" value="<?= htmlspecialchars($membre->getPrenom() ?? '') ?>">
                        <input type="hidden" name="date_naissance_membre" value="<?= $membre->getDateNaissance() ? htmlspecialchars($membre->getDateNaissance()->format('Y-m-d')) : '' ?>">
                        <input type="hidden" name="sexe_membre" value="<?= htmlspecialchars($membre->getSexe() ?? '') ?>">

                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email_membre"
                                    value="<?= htmlspecialchars($membre->getEmail() ?? '') ?>" placeholder="Email" required>
                                <label for="email"><i class="bi bi-envelope"></i> Email *</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="telephone" name="numero_telephone_membre"
                                    value="<?= htmlspecialchars($membre->getNumeroTelephone() ?? '') ?>" placeholder="Numéro de téléphone">
                                <label for="telephone"><i class="bi bi-telephone"></i> Numéro de téléphone</label>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="numeroVoie" name="numero_voie_membre"
                                        value="<?= htmlspecialchars($membre->getNumeroVoie() ?? '') ?>" placeholder="N° de voie">
                                    <label for="numeroVoie"><i class="bi bi-hash"></i> N° voie</label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nomVoie" name="nom_voie_membre"
                                        value="<?= htmlspecialchars($membre->getNomVoie() ?? '') ?>" placeholder="Nom de la voie">
                                    <label for="nomVoie"><i class="bi bi-signpost-2"></i> Nom de la voie</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="codePostal" name="code_postal_membre"
                                        value="<?= htmlspecialchars($membre->getCodePostal() ?? '') ?>" placeholder="Code postal">
                                    <label for="codePostal"><i class="bi bi-mailbox"></i> CP</label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ville" name="ville_membre"
                                        value="<?= htmlspecialchars($membre->getVille() ?? '') ?>" placeholder="Ville">
                                    <label for="ville"><i class="bi bi-geo-alt"></i> Ville</label>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($error) && $error != ""): ?>
                            <div class="alert alert-danger mt-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex gap-2 mt-4">
                            <a href="/avva-admin/liste-membres" class="btn btn-outline-secondary btn-lg w-50">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg w-50 shadow-sm">
                                <i class="bi bi-save me-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>