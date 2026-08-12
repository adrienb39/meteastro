<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10 col-md-11">
            <div class="card p-4 p-md-5 border-0 shadow-lg rounded-4 mt-4 mb-4">
                <div class="card-body">
                    <h2 class="text-center mb-5 text-dark fw-bolder">
                        <i class="fas fa-id-card me-3 text-info"></i> Enregistrement d'un Nouveau Membre
                    </h2>
                    <p class="text-center text-muted mb-4">
                        <small>Veuillez renseigner toutes les informations de licence et personnelles.</small>
                    </p>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2 flex-shrink-0"></i>
                            <div class="small fw-semibold"><?= $_SESSION['error_message']; ?></div>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success d-flex align-items-center rounded-3 mb-4" role="alert">
                            <i class="fas fa-check-circle me-2 flex-shrink-0"></i>
                            <div class="small fw-semibold"><?= $_SESSION['success_message']; ?></div>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <form action="" class="needs-validation" method="post" novalidate>
                        <h5
                            class="mt-4 mb-3 text-secondary border-bottom border-primary pb-2 d-flex align-items-center">
                            <i class="fas fa-user me-2"></i> Identité & Licence
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="numero_licence_membre" id="numero_licence_membre"
                                        class="form-control form-control-lg" placeholder="Numéro de licence" required>
                                    <label for="numero_licence_membre">Numéro de licence</label>
                                    <div class="invalid-feedback">Veuillez indiquer le numéro de licence.</div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="date" name="date_naissance_membre" id="date_naissance_membre"
                                        class="form-control form-control-lg" required>
                                    <label for="date_naissance_membre" class="form-label text-muted small mb-1">Date de
                                        naissance</label>
                                    <div class="invalid-feedback">Veuillez indiquer la date de naissance.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select form-select-lg" id="sexe_membre" name="sexe_membre"
                                        required>
                                        <option value="" selected disabled>Sexe</option>
                                        <option value="M">Masculin</option>
                                        <option value="F">Féminin</option>
                                    </select>
                                    <label for="sexe_membre" class="form-label text-muted small mb-1">Sexe</label>
                                    <div class="invalid-feedback">Veuillez indiquer le sexe.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="nom_membre" id="nom_membre"
                                        class="form-control"
                                        placeholder="Nom" required>
                                    <label for="nom_membre">Nom</label>
                                    <div class="invalid-feedback">Veuillez indiquer le nom.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="prenom_membre" id="prenom_membre"
                                        class="form-control"
                                        placeholder="Prénom" required>
                                    <label for="prenom_membre">Prénom</label>
                                    <div class="invalid-feedback">Veuillez indiquer le prénom.</div>
                                </div>
                            </div>
                        </div>

                        <h5
                            class="mt-5 mb-3 text-secondary border-bottom border-primary pb-2 d-flex align-items-center">
                            <i class="fas fa-address-book me-2"></i> Contact
                        </h5>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" name="email_membre" id="email_membre"
                                        class="form-control"
                                        placeholder="Email" required>
                                    <label for="email_membre">Email</label>
                                    <div class="invalid-feedback">Veuillez indiquer l'email.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" name="numero_telephone_membre" id="numero_telephone_membre"
                                        class="form-control"
                                        placeholder="Numéro de téléphone" required>
                                    <label for="numero_telephone_membre">Numéro de téléphone</label>
                                    <div class="invalid-feedback">Veuillez indiquer le numéro de téléphone.</div>
                                </div>
                            </div>
                        </div>


                        <h5
                            class="mt-5 mb-3 text-secondary border-bottom border-primary pb-2 d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2"></i> Adresse Postale
                        </h5>
                        <div class="row g-3">
                            <div class="col-3">
                                <div class="form-floating">
                                    <input type="text" name="numero_voie_membre" id="numero_voie_membre"
                                        class="form-control" placeholder="Numéro de voie"
                                        required>
                                    <label for="numero_voie_membre">Numéro de voie</label>
                                    <div class="invalid-feedback">Veuillez indiquer le numéro de voie.</div>
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="form-floating">
                                    <input type="text" name="nom_voie_membre" id="nom_voie_membre"
                                        class="form-control"
                                        placeholder="Nom de la voie" required>
                                    <label for="nom_voie_membre">Nom de la voie</label>
                                    <div class="invalid-feedback">Veuillez indiquer le nom de la voie.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="code_postal_membre" id="code_postal_membre"
                                        class="form-control"
                                        placeholder="Code postal" required>
                                    <label for="code_postal_membre">Code postal</label>
                                    <div class="invalid-feedback">Veuillez indiquer le code postal.</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" name="ville_membre" id="ville_membre"
                                        class="form-control"
                                        placeholder="Ville" required>
                                    <label for="ville_membre">Ville</label>
                                    <div class="invalid-feedback">Veuillez indiquer la ville.</div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($error) && $error != ""): ?>
                            <div class="alert alert-danger mt-4 small" role="alert"><?= $error; ?></div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-5 rounded-pill shadow">
                            <i class="fas fa-check-double me-2"></i> Valider l'inscription
                        </button>
                        <p class="paragraph-modern">Une clé d'activation va être envoyé</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>