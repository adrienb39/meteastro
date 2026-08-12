<div class="container content-section-page py-5">
    <div class="content row justify-content-center">
        <div class="col-lg-10">

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div><?= htmlspecialchars($_SESSION['error_message']); ?></div>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?= htmlspecialchars($_SESSION['success_message']); ?></div>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <a href="/page/randos" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Retour sur la page des randonnées
            </a>

            <header class="text-center mb-5">
                <h1 class="display-4 fw-bolder text-primary">
                    <i class="fas fa-road me-3"></i> INSCRIPTION ROUTE
                </h1>
                <h2 class="fs-3 mt-3 text-light">
                    <?= htmlspecialchars($randonnee->getTitre()); ?>
                </h2>
                <p class="text-light lead">
                    <i class="fas fa-mountain me-1"></i> Dénivelé positif :
                    <span class="fw-bold text-light">
                        <?= htmlspecialchars($circuitRandonnee->getDenivelePositif()); ?>
                        <?= $circuitRandonnee->getDenivelePositif() > 1 ? 'mètres' : 'mètre' ?>
                    </span>
                </p>
            </header>

            <form class="needs-validation" method="POST" action="" novalidate>

                <input type="hidden" name="parcours_route_id" value="<?= htmlspecialchars($circuitId); ?>">

                <div id="participants-group" class="mb-4">

                    <div class="card shadow-lg mb-4 participant-card">
                        <div class="card-header bg-primary text-white p-3">
                            <h3 class="h5 mb-0"><i class="fas fa-user-check me-2"></i> Participant Principal</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4">Informations Personnelles</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="nom-0" class="form-label">Nom*</label>
                                    <input type="text" name="nom[]" id="nom-0" class="form-control" required>
                                    <div class="invalid-feedback">Veuillez indiquer votre nom.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="prenom-0" class="form-label">Prénom*</label>
                                    <input type="text" name="prenom[]" id="prenom-0" class="form-control" required>
                                    <div class="invalid-feedback">Veuillez indiquer votre prénom.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="sexe-0" class="form-label">Sexe*</label>
                                    <select class="form-select" name="sexe[]" id="sexe-0" required>
                                        <option value="">--- Choisir le sexe ---</option>
                                        <option value="M">Masculin</option>
                                        <option value="F">Féminin</option>
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner votre sexe.</div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label for="date_naissance-0" class="form-label">Date de naissance*</label>
                                    <input type="date" name="date_naissance[]" id="date_naissance-0"
                                        class="form-control" required>
                                    <div class="invalid-feedback">Veuillez indiquer votre date de naissance.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="num_tel-0" class="form-label">Numéro de téléphone*</label>
                                    <input type="tel" name="num_tel[]" id="num_tel-0" class="form-control" required
                                        pattern="[0-9]{10}">
                                    <div class="invalid-feedback">Veuillez indiquer un numéro de téléphone valide (10
                                        chiffres).</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="email-0" class="form-label">Email*</label>
                                    <input type="email" name="email[]" id="email-0" class="form-control" required>
                                    <div class="invalid-feedback">Veuillez indiquer un email valide.</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h4 class="card-title text-secondary mb-4"><i class="fas fa-id-card-alt me-2"></i> Licence
                                (Optionnel)</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="licence_ffvelo_club-0" class="form-label">FFVélo / Club</label>
                                    <input type="text" name="licence_ffvelo_club[]" id="licence_ffvelo_club-0"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="num_licence-0" class="form-label">Numéro de licence</label>
                                    <input type="text" name="num_licence[]" id="num_licence-0" class="form-control">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-12">
                                    <label for="autre_federation_club-0" class="form-label">Autre fédération /
                                        Club</label>
                                    <input type="text" name="autre_federation_club[]" id="autre_federation_club-0"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-lg mb-4">
                        <div class="card-header bg-secondary text-white p-3">
                            <h3 class="h5 mb-0"><i class="fas fa-home me-2"></i> Adresse de Contact</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="adresse" class="form-label">Adresse*</label>
                                    <input type="text" name="adresse" id="adresse" class="form-control" required>
                                    <div class="invalid-feedback">Veuillez indiquer votre adresse complète.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="code_postal" class="form-label">Code postal*</label>
                                    <input type="text" name="code_postal" id="code_postal" class="form-control" required
                                        pattern="\d{5}">
                                    <div class="invalid-feedback">Veuillez indiquer un code postal valide (5 chiffres).
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="ville" class="form-label">Ville*</label>
                                    <input type="text" name="ville" id="ville" class="form-control" required>
                                    <div class="invalid-feedback">Veuillez indiquer votre ville.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-lg mb-4">
                        <div class="card-header bg-danger text-white p-3">
                            <h3 class="h5 mb-0"><i class="fas fa-heartbeat me-2"></i> Informations d'Urgence</h3>
                        </div>
                        <div class="card-body">
                            <div class="col-12">
                                <label for="nom_prenom_tel" class="form-label">Personne à prévenir en cas d’accident
                                    (Nom / Prénom / Tél)*</label>
                                <input type="text" name="nom_prenom_tel" id="nom_prenom_tel" class="form-control"
                                    required>
                                <div class="invalid-feedback">Veuillez indiquer les coordonnées de la personne à
                                    prévenir.</div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-between mb-4 mt-5">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-credit-card me-2"></i> Finaliser l'inscription (Passer au paiement)
                    </button>
                    <button type="button" id="ajouter-membre" class="btn btn-secondary btn-lg shadow-sm">
                        <i class="fas fa-user-plus me-2"></i> Ajouter un autre membre (Inscrire un ami)
                    </button>
                </div>

                <div class="form-text text-light mt-4">
                    <span class="text-danger fw-bold">*</span> Les champs marqués d'une étoile sont obligatoires.
                </div>
            </form>

            <?php if (!empty($error)) { /* Afficher l'erreur si elle vient d'une validation serveur */ ?>
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-times-circle me-2"></i> <?= htmlspecialchars($error); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    // --- Initialisation de Bootstrap Validation ---
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

    // --- Gestion de l'ajout/suppression de membre ---
    let memberCount = 1; // Commence à 1 car l'index 0 est le membre principal

    document.getElementById('ajouter-membre').addEventListener('click', function () {
        // Utilise le nombre actuel de participants-card pour déterminer le nouvel index
        const currentMemberCards = document.querySelectorAll('.participant-card').length;
        const newIndex = currentMemberCards;
        memberCount = newIndex + 1; // Pour l'affichage "Participant n°2"

        const participantsGroup = document.getElementById('participants-group');

        // Création de la nouvelle carte
        const nouvelleMembreCard = document.createElement('div');
        nouvelleMembreCard.classList.add('card', 'shadow-lg', 'mb-4', 'participant-card', 'member-added');
        nouvelleMembreCard.innerHTML = `
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center p-3">
                <h3 class="h5 mb-0"><i class="fas fa-user me-2"></i> Participant n°${memberCount}</h3>
                <button type="button" class="btn-close btn-close-white supprimer-membre-btn" aria-label="Supprimer ce membre"></button>
            </div>
            <div class="card-body">
                <h4 class="card-title text-info mb-4">Informations Personnelles</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nom-${newIndex}" class="form-label">Nom*</label>
                        <input type="text" name="nom[]" id="nom-${newIndex}" class="form-control" required>
                        <div class="invalid-feedback">Veuillez indiquer le nom.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="prenom-${newIndex}" class="form-label">Prénom*</label>
                        <input type="text" name="prenom[]" id="prenom-${newIndex}" class="form-control" required>
                        <div class="invalid-feedback">Veuillez indiquer le prénom.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="sexe-${newIndex}" class="form-label">Sexe*</label>
                        <select class="form-select" name="sexe[]" id="sexe-${newIndex}" required>
                            <option value="">--- Choisir le sexe ---</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner le sexe.</div>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="date_naissance-${newIndex}" class="form-label">Date de naissance*</label>
                        <input type="date" name="date_naissance[]" id="date_naissance-${newIndex}" class="form-control" required>
                        <div class="invalid-feedback">Veuillez indiquer la date de naissance.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="num_tel-${newIndex}" class="form-label">Numéro de téléphone*</label>
                        <input type="tel" name="num_tel[]" id="num_tel-${newIndex}" class="form-control" required pattern="[0-9]{10}">
                        <div class="invalid-feedback">Veuillez indiquer un numéro de téléphone valide (10 chiffres).</div>
                    </div>
                    <div class="col-md-4">
                        <label for="email-${newIndex}" class="form-label">Email*</label>
                        <input type="email" name="email[]" id="email-${newIndex}" class="form-control" required>
                        <div class="invalid-feedback">Veuillez indiquer un email valide.</div>
                    </div>
                </div>

                <hr class="my-4">

                <h4 class="card-title text-secondary mb-4"><i class="fas fa-id-card-alt me-2"></i> Licence (Optionnel)</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="licence_ffvelo_club-${newIndex}" class="form-label">FFVélo / Club</label>
                        <input type="text" name="licence_ffvelo_club[]" id="licence_ffvelo_club-${newIndex}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="num_licence-${newIndex}" class="form-label">Numéro de licence</label>
                        <input type="text" name="num_licence[]" id="num_licence-${newIndex}" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label for="autre_federation_club-${newIndex}" class="form-label">Autre fédération / Club</label>
                        <input type="text" name="autre_federation_club[]" id="autre_federation_club-${newIndex}" class="form-control">
                    </div>
                </div>
            </div>
        `;

        // Insère la nouvelle carte AVANT la carte d'adresse pour garder l'adresse et l'urgence en bas
        const firstNonParticipantCard = participantsGroup.querySelector('.card:not(.participant-card)');
        if (firstNonParticipantCard) {
            participantsGroup.insertBefore(nouvelleMembreCard, firstNonParticipantCard);
        } else {
            // Si pour une raison quelconque l'adresse n'est pas trouvée, l'ajouter à la fin.
            participantsGroup.appendChild(nouvelleMembreCard);
        }


        // Attache l'événement de suppression au nouveau bouton
        nouvelleMembreCard.querySelector('.supprimer-membre-btn').addEventListener('click', function () {
            nouvelleMembreCard.remove();
            // Optionnel: Réindexer les titres (Participant n°2, Participant n°3) après suppression
            updateMemberTitles();
        });
    });

    function updateMemberTitles() {
        const memberCards = document.querySelectorAll('.participant-card');
        memberCards.forEach((card, index) => {
            const titleElement = card.querySelector('.card-header h3');
            if (titleElement) {
                // Le premier participant a un titre différent
                if (index === 0) {
                    titleElement.innerHTML = '<i class="fas fa-user-check me-2"></i> Participant Principal';
                } else {
                    titleElement.innerHTML = `<i class="fas fa-user me-2"></i> Participant n°${index + 1}`;
                }
            }
        });
    }
</script>