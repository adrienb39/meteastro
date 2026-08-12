<div class="container-fluid main-container-mobile py-3 py-md-4 px-2 px-sm-3 px-md-4">

    <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div
                class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-3">
                <div>
                    <h1 class="h4 h3-md fw-bold text-dark mb-1">
                        <i class="fas fa-users text-primary me-2"></i>Gestion des Membres
                    </h1>
                    <p class="text-muted small mb-0 d-none d-sm-block">
                        Consultez, importez et gérez l'ensemble des membres actifs de votre plateforme.
                    </p>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-lg-auto">
                    <a href="/avva-admin/creer-membre" class="btn btn-primary rounded-3 shadow-sm px-3 w-100 w-sm-auto">
                        <i class="fas fa-user-plus me-2"></i>Nouveau Membre
                    </a>

                    <div class="d-flex gap-2 w-100 w-sm-auto">
                        <button type="button"
                            class="btn btn-outline-warning text-dark rounded-3 shadow-sm px-3 flex-fill"
                            data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import me-1 me-sm-2"></i>Importer
                        </button>

                        <a href="/avva-admin/export-membres"
                            class="btn btn-outline-success rounded-3 shadow-sm px-3 flex-fill">
                            <i class="fas fa-file-excel me-1 me-sm-2"></i>Exporter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="card-header bg-white border-0 p-3 p-md-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="tableSearch" class="form-control bg-light border-0 fs-6"
                            placeholder="Rechercher un membre...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="membresTable">
                    <thead class="bg-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th scope="col" class="ps-3 ps-md-4" style="width: 50px;">#</th>
                            <th scope="col">Membre</th>
                            <th scope="col" class="d-none d-sm-table-cell">Licence / Offre</th>
                            <th scope="col" class="d-none d-md-table-cell">Coordonnées</th>
                            <th scope="col" class="text-end pe-3 pe-md-4" style="width: 110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (!empty($membres)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($membres as $membre): ?>
                                <?php
                                $nomComplet = trim(($membre->getPrenom() ?? '') . ' ' . ($membre->getNom() ?? ''));
                                $email = $membre->getEmail();
                                $tel = $membre->getNumeroTelephone();

                                $adresseParts = array_filter([
                                    $membre->getNumeroVoie(),
                                    $membre->getNomVoie(),
                                    trim(($membre->getCodePostal() ?? '') . ' ' . ($membre->getVille() ?? ''))
                                ]);
                                $adresse = !empty($adresseParts) ? implode(' ', $adresseParts) : 'Non renseignée';
                                $dateNaissance = $membre->getDateNaissance() ? $membre->getDateNaissance()->format('d/m/Y') : 'Non renseignée';
                                $dateEssai = ($membre->getPlan() === 'trial' && $membre->getDateFinEssai()) ? $membre->getDateFinEssai()->format('d/m/Y') : null;
                                ?>
                                <tr>
                                    <td class="ps-3 ps-md-4 text-muted small"><?= $i++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2 me-md-3 flex-shrink-0"
                                                style="width: 36px; height: 36px; font-weight: 600;">
                                                <?= !empty($nomComplet) ? mb_substr($membre->getNom() ?? $email, 0, 1) : 'U' ?>
                                            </div>
                                            <div class="text-truncate" style="max-width: 180px;">
                                                <div class="fw-bold text-dark text-truncate">
                                                    <?= !empty($nomComplet) ? htmlspecialchars($nomComplet) : '<em>Sans nom</em>' ?>
                                                </div>
                                                <div class="small text-muted text-truncate"><?= htmlspecialchars($email) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <?php if ($membre->getNumeroLicence()): ?>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 fw-normal">
                                                N° <?= htmlspecialchars($membre->getNumeroLicence()) ?>
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 fw-normal">
                                                <?= htmlspecialchars(ucfirst($membre->getPlan() ?? 'Essai')) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="small">
                                            <?php if ($tel): ?>
                                                <div class="text-dark">
                                                    <i class="fas fa-phone me-1 text-muted"></i><?= htmlspecialchars($tel) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-phone me-1 text-muted"></i>-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3 pe-md-4 text-nowrap">
                                        <button type="button"
                                            class="btn btn-sm btn-light border text-secondary rounded-circle me-1 btn-view-details"
                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                            data-name="<?= htmlspecialchars($nomComplet ?: $email) ?>"
                                            data-email="<?= htmlspecialchars($email) ?>"
                                            data-phone="<?= htmlspecialchars($tel ?: 'Non renseigné') ?>"
                                            data-licence="<?= htmlspecialchars($membre->getNumeroLicence() ?: 'Aucune') ?>"
                                            data-plan="<?= htmlspecialchars(strtoupper($membre->getPlan() ?? 'TRIAL')) ?>"
                                            data-essai="<?= htmlspecialchars($dateEssai ?? '') ?>"
                                            data-naissance="<?= htmlspecialchars($dateNaissance) ?>"
                                            data-sexe="<?= htmlspecialchars($membre->getSexe() ?: 'Non renseigné') ?>"
                                            data-adresse="<?= htmlspecialchars($adresse) ?>" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="/avva-admin/modifier-membre/<?= $membre->getId() ?>"
                                            class="btn btn-sm btn-light border text-primary rounded-circle" title="Modifier">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-2 text-black-50 d-block"></i>
                                    Aucun membre enregistré.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold fs-6 fs-sm-5" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Importer des Membres
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/avva-admin/import-membres" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-3 p-md-4">
                    <div class="mb-3">
                        <label for="fichier_import" class="form-label fw-semibold">Fichier (.csv)</label>
                        <input class="form-control rounded-3" type="file" id="fichier_import" name="fichier_import"
                            accept=".csv, .xlsx, .xls" required>
                    </div>
                    <div class="alert alert-light border border-secondary-subtle small text-muted mb-0 rounded-3">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        Structure attendue pour les entêtes du fichier :<br>
                        <div class="bg-white p-2 border rounded mt-2 font-monospace"
                            style="font-size: 0.75rem; overflow-x: auto;">
                            numero_licence_membre, nom_membre, prenom_membre, date_naissance_membre, sexe_membre,
                            numero_voie_membre, nom_voie_membre, code_postal_membre, ville_membre,
                            numero_telephone_membre, email_membre
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 w-100 w-sm-auto mb-2 mb-sm-0"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning rounded-3 fw-bold text-dark w-100 w-sm-auto px-4">
                        <i class="fas fa-upload me-1"></i>Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold fs-6 fs-sm-5">
                    <i class="fas fa-user-id-card me-2"></i>Fiche Membre
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="text-center mb-3">
                    <div class="avatar-circle bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 56px; height: 56px; font-size: 1.3rem; font-weight: bold;">
                        <span id="mAvatar">?</span>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark" id="mName">-</h5>
                    <span class="badge bg-secondary rounded-pill mt-1" id="mPlan">-</span>
                    <small class="text-muted d-block mt-1" id="mEssaiWrapper" style="display:none;">
                        Fin de l'essai le : <span id="mEssai"></span>
                    </small>
                </div>

                <div class="list-group list-group-flush rounded-3 border small">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="fas fa-id-badge me-2"></i>Licence</span>
                        <span class="fw-semibold text-dark" id="mLicence">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="fas fa-envelope me-2"></i>Email</span>
                        <a href="#" class="fw-semibold text-decoration-none text-truncate ms-2" id="mEmail"
                            style="max-width: 180px;">-</a>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="fas fa-phone me-2"></i>Téléphone</span>
                        <span class="fw-semibold text-dark" id="mPhone">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="fas fa-birthday-cake me-2"></i>Naissance</span>
                        <span class="fw-semibold text-dark" id="mNaissance">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="fas fa-venus-mars me-2"></i>Sexe</span>
                        <span class="fw-semibold text-dark" id="mSexe">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Adresse</span>
                        <span class="fw-semibold text-dark text-end ms-2" id="mAdresse">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 w-100" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Marge mobile de 55px sous 768px */
    @media (max-width: 767.98px) {
        .main-container-mobile {
            margin-top: 55px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const detailModal = document.getElementById('detailModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', (event) => {
                const button = event.relatedTarget;

                const name = button.getAttribute('data-name');
                const email = button.getAttribute('data-email');

                document.getElementById('mName').textContent = name;
                document.getElementById('mAvatar').textContent = name.charAt(0).toUpperCase();
                document.getElementById('mEmail').textContent = email;
                document.getElementById('mEmail').href = `mailto:${email}`;
                document.getElementById('mPhone').textContent = button.getAttribute('data-phone');
                document.getElementById('mLicence').textContent = button.getAttribute('data-licence');
                document.getElementById('mPlan').textContent = button.getAttribute('data-plan');
                document.getElementById('mNaissance').textContent = button.getAttribute('data-naissance');
                document.getElementById('mSexe').textContent = button.getAttribute('data-sexe');
                document.getElementById('mAdresse').textContent = button.getAttribute('data-adresse');

                const essai = button.getAttribute('data-essai');
                const essaiWrapper = document.getElementById('mEssaiWrapper');
                if (essai) {
                    document.getElementById('mEssai').textContent = essai;
                    essaiWrapper.style.display = 'block';
                } else {
                    essaiWrapper.style.display = 'none';
                }
            });
        }

        const searchInput = document.getElementById('tableSearch');
        const tableRows = document.querySelectorAll('#membresTable tbody tr');

        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                const term = e.target.value.toLowerCase();
                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>