<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12">

            <header class="mb-5 border-bottom pb-3 d-flex justify-content-between align-items-center">
                <h1 class="display-6 fw-bold text-dark">
                    <i class="fa-solid fa-person-biking text-primary me-2"></i> Gestion des Randonnées
                </h1>
            </header>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="card shadow-lg border-0 rounded-4 mb-5 bg-white">
                <div
                    class="card-header <?= $isEditing ? 'bg-warning text-dark' : 'bg-success text-white' ?> p-3 rounded-top-4">
                    <h3 class="mb-0 fw-light">
                        <i class="fa-solid fa-<?= $isEditing ? 'pen-to-square' : 'plus-circle' ?> me-2"></i>
                        <?= $isEditing ? 'Modification de la Randonnée : ' . htmlspecialchars($titre) : 'Ajouter une nouvelle Randonnée' ?>
                    </h3>
                </div>

                <div class="card-body p-4 p-md-5">

                    <form method="post"
                        action="<?= $isEditing ? "/avva-admin/randonnee/modifier/{$randonneeEnEdition->getId()}" : "/avva-admin/randonnee/creer" ?>"
                        enctype="multipart/form-data">

                        <ul class="nav nav-tabs nav-tabs-admin mb-4" id="randonneeTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="base-tab" data-bs-toggle="tab"
                                    data-bs-target="#base" type="button" role="tab" aria-controls="base"
                                    aria-selected="true">
                                    <i class="fa-solid fa-layer-group me-1"></i> Infos de Base
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="circuits-tab" data-bs-toggle="tab"
                                    data-bs-target="#circuits" type="button" role="tab" aria-controls="circuits"
                                    aria-selected="false">
                                    <i class="fa-solid fa-route me-1"></i> Circuits (<span
                                        id="nb-circuits-indicator"><?= count($circuitsData) ?></span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="event-tab" data-bs-toggle="tab" data-bs-target="#event"
                                    type="button" role="tab" aria-controls="event" aria-selected="false">
                                    <i class="fa-solid fa-bullhorn me-1"></i> Événement & Annulation
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo"
                                    type="button" role="tab" aria-controls="seo" aria-selected="false">
                                    <i class="fa-solid fa-gear me-1"></i> Administration
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="randonneeTabContent">

                            <div class="tab-pane fade show active" id="base" role="tabpanel" aria-labelledby="base-tab">

                                <h4 class="mb-3 text-primary border-bottom pb-1"><i
                                        class="fa-solid fa-info-circle me-1"></i> Infos Générales</h4>

                                <div class="form-floating mb-4">
                                    <input type="text" name="titre_randonnee" id="titre_randonnee" class="form-control"
                                        placeholder="Titre de la randonnée" value="<?= htmlspecialchars($titre) ?>"
                                        required>
                                    <label for="titre_randonnee">Titre de la randonnée</label>
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="text" name="lieu_depart_randonnee" id="lieu_depart_randonnee"
                                        class="form-control" placeholder="Lieu de départ (ville, point GPS, etc.)"
                                        value="<?= htmlspecialchars($lieuDepart) ?>" required>
                                    <label for="lieu_depart_randonnee"><i class="fa-solid fa-map-pin me-1"></i> Lieu de
                                        départ</label>
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="text" name="coordonnees_gps_randonnee" id="coordonnees_gps_randonnee"
                                        class="form-control" placeholder="Ex: 44.8378, -0.5791 (Latitude, Longitude)"
                                        value="<?= htmlspecialchars($coordonneesGps ?? '') ?>">
                                    <label for="coordonnees_gps_randonnee"><i class="fa-solid fa-location-dot me-1"></i>
                                        Coordonnées GPS (Optionnel)</label>
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="datetime-local" name="date_depart_randonnee" id="date_depart_randonnee"
                                        class="form-control" required value="<?= htmlspecialchars($dateDepart) ?>">
                                    <label for="date_depart_randonnee">
                                        <i class="fa-solid fa-calendar-check me-1"></i> Date et heure de l'événement
                                    </label>
                                </div>

                                <div class="mb-4">
                                    <label for="description_courte_randonnee" class="form-label text-muted">
                                        <i class="fa-solid fa-scroll me-1"></i> Description Courte (Résumé)
                                    </label>
                                    <textarea name="description_courte_randonnee" id="description_courte_randonnee"
                                        class="form-control"
                                        placeholder="Un court résumé pour les aperçus (max 255 caractères)"
                                        rows="3"><?= htmlspecialchars($descriptionCourte) ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="description_complete_randonnee" class="form-label text-muted">
                                        <i class="fa-solid fa-newspaper me-1"></i> Description Complète / Détails de la
                                        randonnée
                                    </label>
                                    <textarea name="description_complete_randonnee" id="description_complete_randonnee"
                                        class="form-control" placeholder="Description complète de la randonnée"
                                        data-summernote="true"
                                        rows="8"><?= htmlspecialchars($descriptionComplete) ?></textarea>
                                </div>

                                <hr class="my-5">

                                <h4 class="mb-3 text-secondary border-bottom pb-1"><i
                                        class="fa-solid fa-palette me-1"></i> Affichage & Médias</h4>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="image_principale_randonnee" class="form-label">Image Principale
                                            (JPG, PNG)</label>
                                        <input type="file" name="image_principale_randonnee"
                                            id="image_principale_randonnee" class="form-control"
                                            value="<?= htmlspecialchars($imagePrincipale ?? '') ?>">
                                        <?php if ($imagePrincipale): ?>
                                            <small class="text-muted mt-1 d-block">Image actuelle :
                                                <?= basename($imagePrincipale) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="couleur_thematique_randonnee" class="form-label">Couleur
                                            Thématique</label>
                                        <input type="color" name="couleur_thematique_randonnee"
                                            id="couleur_thematique_randonnee" class="form-control form-control-color"
                                            value="<?= htmlspecialchars($couleurThematique ?? '#4CAF50') ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="modele_page_randonnee" class="form-label">Modèle de Page</label>
                                        <select class="form-select" id="modele_page_randonnee"
                                            name="modele_page_randonnee" required>
                                            <option value="tpl_defaut" <?= ($modelePage === 'tpl_defaut') ? 'selected' : '' ?>>
                                                Standard (Carte + Inscription + Détails)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4 d-flex align-items-center">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="afficher_carte_randonnee" name="afficher_carte_randonnee"
                                                <?= (($afficherCarte ?? true) ? 'checked' : '') ?>>
                                            <label class="form-check-label" for="afficher_carte_randonnee">Afficher la
                                                carte (Globale)</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="circuits" role="tabpanel" aria-labelledby="circuits-tab">

                                <h4 class="mb-3 text-success border-bottom pb-1"><i class="fa-solid fa-route me-1"></i>
                                    Gestion des Différents Circuits</h4>
                                <p class="text-muted small mb-4">Définissez un ou plusieurs parcours pour cette
                                    randonnée. Le prix d'inscription est géré par circuit.</p>

                                <div id="circuits-container">
                                    <?php
                                    // Logic PHP pour charger les circuits existants ou en créer un vide
                                    // Si on est en mode édition, on prend les circuits de l'objet Doctrine.
                                    if ($isEditing) {
                                        $circuitsData = $randonneeEnEdition->getCircuits()->toArray() ?? [];
                                    } else {
                                        // En mode création, $circuitsData vient du contrôleur (avec le prix par défaut)
                                    }

                                    $circuitIndex = 0;
                                    ?>

                                    <?php foreach ($circuitsData as $circuit):
                                        // Récupération des données, en gérant les objets Doctrine ou les tableaux PHP
                                        $isObject = is_object($circuit);
                                        $nom = $isObject ? ($circuit->getNom() ?? '') : $circuit['nom'];
                                        $dist = $isObject ? ($circuit->getDistanceKm() ?? '') : $circuit['distance_km'];
                                        $duree = $isObject ? ($circuit->getDureeHeures() ?? '') : $circuit['duree_heures'];
                                        $deniv = $isObject ? ($circuit->getDenivelePositif() ?? '') : $circuit['denivele_positif'];
                                        $diff = $isObject ? ($circuit->getDifficulte() ?? 'Modéré') : $circuit['difficulte'];
                                        $type = $isObject ? $circuit->getType() : $circuit['type'];
                                        $gpx = $isObject ? ($circuit->getFichierGpx() ?? '') : $circuit['fichier_gpx_path']; // $fichier_gpx_path en POST, getFichierGpx en Doctrine
                                        $principal = $isObject ? ($circuit->isEstPrincipal() ?? false) : $circuit['est_principal'];

                                        // NOUVEAU: Récupération et conversion du prix pour affichage (Centimes -> Euros)
                                        $prixInscriptionMoins18AnsLicencieCentimes = $isObject ? ($circuit->getPrixInscriptionMoins18AnsLicencieCentimes() ?? 0) : ($circuit['prix_inscription_moins_18_ans_licencie_centimes'] ?? 000);
                                        $prixInscriptionMoins18AnsLicencie = number_format($prixInscriptionMoins18AnsLicencieCentimes / 100, 2, '.', '');

                                        $prixInscriptionMoins18AnsNonLicencieCentimes = $isObject ? ($circuit->getPrixInscriptionMoins18AnsNonLicencieCentimes() ?? 0) : ($circuit['prix_inscription_moins_18_ans_non_licencie_centimes'] ?? 400);
                                        $prixInscriptionMoins18AnsNonLicencie = number_format($prixInscriptionMoins18AnsNonLicencieCentimes / 100, 2, '.', '');

                                        $prixInscriptionAdulteLicencieCentimes = $isObject ? ($circuit->getPrixInscriptionAdulteLicencieCentimes() ?? 0) : ($circuit['prix_inscription_adulte_licencie_centimes'] ?? 700);
                                        $prixInscriptionAdulteLicencie = number_format($prixInscriptionAdulteLicencieCentimes / 100, 2, '.', '');

                                        $prixInscriptionAdulteNonLicencieCentimes = $isObject ? ($circuit->getPrixInscriptionAdulteNonLicencieCentimes() ?? 0) : ($circuit['prix_inscription_adulte_non_licencie_centimes'] ?? 1000);
                                        $prixInscriptionAdulteNonLicencie = number_format($prixInscriptionAdulteNonLicencieCentimes / 100, 2, '.', '');
                                        ?>
                                        <div class="circuit-card card border-success mb-4"
                                            data-index="<?= $circuitIndex ?>">
                                            <div
                                                class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                                                <h5 class="mb-0 card-title-circuit">
                                                    <i class="fa-solid fa-route me-1"></i> Circuit n°<span
                                                        class="circuit-number"><?= $circuitIndex + 1 ?></span>
                                                    <span class="badge bg-light text-dark ms-2 circuit-principal-badge"
                                                        style="display: <?= $principal ? 'inline-block' : 'none' ?>;">Principal</span>
                                                </h5>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-light btn-remove-circuit"
                                                    title="Supprimer ce circuit" <?= (count($circuitsData) == 1) ? 'disabled' : '' ?>
                                                    style="display: <?= count($circuitsData) == 1 ? 'none' : 'block' ?>;">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <input type="hidden" name="circuits[<?= $circuitIndex ?>][id]"
                                                    value="<?= $isObject ? $circuit->getId() : '' ?>">

                                                <div class="form-floating mb-4">
                                                    <input type="text" name="circuits[<?= $circuitIndex ?>][nom]"
                                                        id="nom_circuit_<?= $circuitIndex ?>" class="form-control"
                                                        placeholder="Nom du circuit (ex: Petit parcours, 15 km)"
                                                        value="<?= htmlspecialchars($nom) ?>" required>
                                                    <label for="nom_circuit_<?= $circuitIndex ?>">Nom du Circuit</label>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.1"
                                                                name="circuits[<?= $circuitIndex ?>][distance_km]"
                                                                id="distance_km_<?= $circuitIndex ?>" class="form-control"
                                                                placeholder="Distance (km)"
                                                                value="<?= htmlspecialchars($dist) ?>" required>
                                                            <label for="distance_km_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-arrows-left-right me-1"></i> Distance
                                                                (km)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.1"
                                                                name="circuits[<?= $circuitIndex ?>][duree_heures]"
                                                                id="duree_heures_<?= $circuitIndex ?>" class="form-control"
                                                                placeholder="Durée estimée (heures)"
                                                                value="<?= htmlspecialchars($duree) ?>" required>
                                                            <label for="duree_heures_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-clock me-1"></i> Durée
                                                                (heures)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number"
                                                                name="circuits[<?= $circuitIndex ?>][denivele_positif]"
                                                                id="denivele_positif_<?= $circuitIndex ?>"
                                                                class="form-control" placeholder="Dénivelé positif (mètres)"
                                                                value="<?= htmlspecialchars($deniv) ?>" required>
                                                            <label for="denivele_positif_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-arrow-up-wide-short me-1"></i>
                                                                Dénivelé Positif (m)</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.01" min="0"
                                                                name="circuits[<?= $circuitIndex ?>][prix_inscription_moins_18_ans_licencie]"
                                                                id="prix_inscription_moins_18_ans_licencie_<?= $circuitIndex ?>"
                                                                class="form-control"
                                                                placeholder="Prix d'inscription (ex: 15.00)"
                                                                value="<?= htmlspecialchars($prixInscriptionMoins18AnsLicencie) ?>"
                                                                required>
                                                            <label
                                                                for="prix_inscription_moins_18_ans_licencie_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-euro-sign me-1"></i> Prix
                                                                d'inscription pour les moins de 18 ans licencié</label>
                                                        </div>
                                                        <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                                                            un point '.' pour les décimales.</small>
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.01" min="0"
                                                                name="circuits[<?= $circuitIndex ?>][prix_inscription_moins_18_ans_non_licencie]"
                                                                id="prix_inscription_moins_18_ans_non_licencie_<?= $circuitIndex ?>"
                                                                class="form-control"
                                                                placeholder="Prix d'inscription (ex: 15.00)"
                                                                value="<?= htmlspecialchars($prixInscriptionMoins18AnsNonLicencie) ?>"
                                                                required>
                                                            <label
                                                                for="prix_inscription_moins_18_ans_non_licencie_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-euro-sign me-1"></i> Prix
                                                                d'inscription pour les moins de 18 ans non licencié</label>
                                                        </div>
                                                        <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                                                            un point '.' pour les décimales.</small>
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.01" min="0"
                                                                name="circuits[<?= $circuitIndex ?>][prix_inscription_adulte_licencie]"
                                                                id="prix_inscription_adulte_licencie_<?= $circuitIndex ?>"
                                                                class="form-control"
                                                                placeholder="Prix d'inscription (ex: 15.00)"
                                                                value="<?= htmlspecialchars($prixInscriptionAdulteLicencie) ?>"
                                                                required>
                                                            <label
                                                                for="prix_inscription_adulte_licencie_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-euro-sign me-1"></i> Prix
                                                                d'inscription pour les adultes licencié</label>
                                                        </div>
                                                        <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                                                            un point '.' pour les décimales.</small>
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.01" min="0"
                                                                name="circuits[<?= $circuitIndex ?>][prix_inscription_adulte_non_licencie]"
                                                                id="prix_inscription_adulte_non_licencie_<?= $circuitIndex ?>"
                                                                class="form-control"
                                                                placeholder="Prix d'inscription (ex: 15.00)"
                                                                value="<?= htmlspecialchars($prixInscriptionAdulteNonLicencie) ?>"
                                                                required>
                                                            <label
                                                                for="prix_inscription_adulte_non_licencie_<?= $circuitIndex ?>"><i
                                                                    class="fa-solid fa-euro-sign me-1"></i> Prix
                                                                d'inscription pour les adultes licencié</label>
                                                        </div>
                                                        <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                                                            un point '.' pour les décimales.</small>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-floating">
                                                            <select name="circuits[<?= $circuitIndex ?>][type]"
                                                                id="type_<?php $circuitIndex ?>" class="form-select">
                                                                <option value="VTT" <?= ($type === 'VTT') ? 'selected' : '' ?>>
                                                                    VTT</option>
                                                                <option value="GRAVEL" <?= ($type === 'GRAVEL') ? 'selected' : '' ?>>GRAVEL</option>
                                                                <option value="ROUTE" <?= ($type === 'ROUTE') ? 'selected' : '' ?>>ROUTE</option>
                                                                <option value="PÉDESTRE" <?= ($type === 'PÉDESTRE') ? 'selected' : '' ?>>PÉDESTRE</option>
                                                            </select>
                                                            <label for="type_<?= $circuitIndex ?>">
                                                                <i class="fa-solid fa-tags me-1"></i> Type
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-floating">
                                                            <select name="circuits[<?= $circuitIndex ?>][difficulte]"
                                                                id="niveau_difficulte_<?= $circuitIndex ?>"
                                                                class="form-select">
                                                                <option value="Facile" <?= ($diff === 'Facile') ? 'selected' : '' ?>>Facile</option>
                                                                <option value="Modéré" <?= ($diff === 'Modéré') ? 'selected' : '' ?>>Modéré</option>
                                                                <option value="Difficile" <?= ($diff === 'Difficile') ? 'selected' : '' ?>>Difficile</option>
                                                                <option value="Très Difficile" <?= ($diff === 'Très Difficile') ? 'selected' : '' ?>>Très Difficile</option>
                                                            </select>
                                                            <label for="niveau_difficulte_<?= $circuitIndex ?>">
                                                                <i class="fa-solid fa-gauge-high me-1"></i> Niveau de
                                                                difficulté
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center">
                                                    <div class="col-md-7 mb-4">
                                                        <label for="fichier_gpx_<?= $circuitIndex ?>"
                                                            class="form-label">Fichier GPX (Optionnel)</label>
                                                        <input type="file"
                                                            name="circuits[<?= $circuitIndex ?>][fichier_gpx]"
                                                            id="fichier_gpx_<?= $circuitIndex ?>" class="form-control"
                                                            accept=".gpx">
                                                        <?php if ($gpx): ?>
                                                            <small class="text-success d-block mt-1">Fichier actuel :
                                                                <?= htmlspecialchars(basename($gpx)) ?></small>
                                                        <?php endif; ?>
                                                        <input type="hidden"
                                                            name="circuits[<?= $circuitIndex ?>][fichier_gpx_current]"
                                                            value="<?= htmlspecialchars($gpx ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-5 mb-4">
                                                        <div class="form-check form-switch mt-3">
                                                            <input class="form-check-input is-principal-toggle" type="radio"
                                                                role="switch" name="circuits_est_principal"
                                                                id="est_principal_circuit_<?= $circuitIndex ?>"
                                                                value="<?= $circuitIndex ?>" <?= $principal ? 'checked' : '' ?> required>
                                                            <label class="form-check-label"
                                                                for="est_principal_circuit_<?= $circuitIndex ?>">Définir
                                                                comme circuit Principal</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <?php $circuitIndex++; endforeach; ?>
                                </div>

                                <?php if (!$isEditing): ?>
                                    <button type="button" id="add-circuit-btn"
                                        class="btn btn-outline-success rounded-pill w-100 p-3">
                                        <i class="fa-solid fa-plus-circle me-2"></i> Ajouter un autre circuit
                                    </button>
                                <?php endif; ?>

                            </div>

                            <div class="tab-pane fade" id="event" role="tabpanel" aria-labelledby="event-tab">

                                <h4 class="mb-3 text-info border-bottom pb-1"><i class="fa-solid fa-users me-1"></i>
                                    Gestion des Inscriptions</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-floating">
                                            <input type="number" name="nombre_participants_max_randonnee"
                                                id="nombre_participants_max_randonnee" class="form-control"
                                                placeholder="Max de participants (0 pour illimité)"
                                                value="<?= htmlspecialchars($nombreParticipantsMax ?? 0) ?>">
                                            <label for="nombre_participants_max_randonnee">Participants Max (0 =
                                                illimité)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="form-floating">
                                            <select name="statut_inscription_randonnee"
                                                id="statut_inscription_randonnee" class="form-select">
                                                <option value="Ouvert" <?= (($statutInscription ?? 'Ouvert') === 'Ouvert') ? 'selected' : '' ?>>Ouvert</option>
                                                <option value="Fermé" <?= (($statutInscription ?? 'Ouvert') === 'Fermé') ? 'selected' : '' ?>>Fermé (Complet)</option>
                                                <option value="Préinscription" <?= (($statutInscription ?? 'Ouvert') === 'Préinscription') ? 'selected' : '' ?>>Préinscription
                                                    Requise</option>
                                            </select>
                                            <label for="statut_inscription_randonnee">Statut d'Inscription</label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-5">

                                <h4 class="mb-3 text-danger border-bottom pb-1"><i
                                        class="fa-solid fa-hand-paper me-1"></i> Annulation</h4>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="est_annulee_randonnee" name="est_annulee_randonnee" <?= (($estAnnulee ?? false) ? 'checked' : '') ?>>
                                    <label class="form-check-label text-danger fw-bold"
                                        for="est_annulee_randonnee">Cette randonnée est Annulée</label>
                                </div>

                                <div class="mb-4">
                                    <label for="message_annulation_randonnee" class="form-label text-muted">
                                        <i class="fa-solid fa-comment-dots me-1"></i> Message d'annulation (affiché
                                        publiquement)
                                    </label>
                                    <textarea name="message_annulation_randonnee" id="message_annulation_randonnee"
                                        class="form-control" rows="3"
                                        placeholder="Raison de l'annulation (Météo, problème logistique, etc.)"><?= htmlspecialchars($messageAnnulation ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">

                                <h4 class="mb-3 text-secondary border-bottom pb-1"><i
                                        class="fa-solid fa-user-secret me-1"></i> Administration</h4>

                                <div class="form-floating mb-4">
                                    <select name="statut_publication_randonnee" id="statut_publication_randonnee"
                                        class="form-select">
                                        <option value="Brouillon" <?= (($statutPublication ?? 'Brouillon') === 'Brouillon') ? 'selected' : '' ?>>Brouillon</option>
                                        <option value="Publié" <?= (($statutPublication ?? 'Brouillon') === 'Publié') ? 'selected' : '' ?>>Publié</option>
                                        <option value="Archivé" <?= (($statutPublication ?? 'Brouillon') === 'Archivé') ? 'selected' : '' ?>>Archivé</option>
                                    </select>
                                    <label for="statut_publication_randonnee">Statut de Publication</label>
                                </div>

                                <div class="mb-4">
                                    <label for="notes_internes_randonnee" class="form-label text-muted">
                                        <i class="fa-solid fa-note-sticky me-1"></i> Notes Internes (non publiques)
                                    </label>
                                    <textarea name="notes_internes_randonnee" id="notes_internes_randonnee"
                                        class="form-control" rows="3"
                                        placeholder="Notes pour l'équipe ou l'administration"><?= htmlspecialchars($notesInternes ?? '') ?></textarea>
                                </div>

                            </div>
                        </div>

                        <?php if (isset($error) && $error != ""): ?>
                            <div id="errorHelp" class="mt-4 alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <?php if ($isEditing): ?>
                                <a href="/avva-admin/randonnee"
                                    class="btn btn-outline-secondary rounded-pill me-md-2 d-flex align-items-center">
                                    <i class="fa-solid fa-xmark me-2"></i> Annuler la modification
                                </a>
                            <?php endif; ?>
                            <button type="submit"
                                class="btn btn-lg <?= $isEditing ? 'btn-warning text-dark' : 'btn-success' ?> rounded-pill shadow">
                                <i class="fa-solid fa-save me-2"></i>
                                <?= $isEditing ? 'Mettre à jour la randonnée' : 'Sauvegarder la nouvelle randonnée' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <h2 class="display-6 fw-bold text-dark mt-5 mb-4">
                <i class="fa-solid fa-list-check text-secondary me-2"></i> Randonnées Programmées
            </h2>

            <?php if (!empty($randonnees)): // Utilisation de $randonnees ?>
                <div class="table-responsive bg-white rounded-4 shadow-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 25%;">Titre / Lieu</th>
                                <th scope="col" style="width: 15%;">Départ</th>
                                <th scope="col" style="width: 15%;">Distance / Dénivelé (Principal)</th>
                                <th scope="col" style="width: 10%;">Difficulté (P.)</th>
                                <th scope="col" style="width: 10%;">Statut</th>
                                <th scope="col" style="width: 10%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($randonnees as $randonnee):
                                // Récupération des données du circuit principal pour l'affichage
                                $circuitPrincipal = $randonnee->getCircuits()->filter(fn($c) => $c->isEstPrincipal())->first() ?? null;
                                $dist = $circuitPrincipal ? $circuitPrincipal->getDistanceKm() : 'N/A';
                                $deniv = $circuitPrincipal ? $circuitPrincipal->getDenivelePositif() : 'N/A';
                                $diff = $circuitPrincipal ? $circuitPrincipal->getDifficulte() : 'N/A';
                                $isCurrentEdit = $isEditing && $randonnee->getId() === $randonneeEnEdition->getId();
                                ?>
                                <tr class="<?= $isEditing && $randonnee->getId() === $randonneeEnEdition->getId() ? 'table-warning' : '' ?>">
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($randonnee->getTitre()) ?></span>
                                        <br><small
                                            class="text-muted"><?= htmlspecialchars($randonnee->getLieuDepart()) ?></small>
                                    </td>
                                    <td><?= $randonnee->getDateRandonnee()->format('d/m/Y H:i') ?></td>
                                    <td><?= $dist ?> km / <?= $deniv ?> m</td>
                                    <td><span class="badge bg-secondary"><?= $diff ?></span></td>
                                    <td>
                                        <?php
                                        $status = $randonnee->getStatutPublication();
                                        $class = ['Publié' => 'success', 'Brouillon' => 'warning', 'Archivé' => 'secondary'][$status] ?? 'info';
                                        if ($randonnee->isEstAnnulee())
                                            $class = 'danger';
                                        ?>
                                        <span
                                            class="badge bg-<?= $class ?>"><?= $randonnee->isEstAnnulee() ? 'Annulée' : $status ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="/avva-admin/randonnee/modifier/<?= $randonnee->getId() ?>"
                                            class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger btn-supprimer-randonnee"
                                            title="Supprimer" data-bs-toggle="modal" data-bs-target="#modalSuppression"
                                            data-url="/avva-admin/randonnee/supprimer/<?= $randonnee->getId() ?>"
                                            data-titre-randonnee="<?= htmlspecialchars($randonnee->getTitre()) ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i> Aucune randonnée n'a encore été enregistrée.
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="modal fade" id="modalSuppression" tabindex="-1" aria-labelledby="modalSuppressionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white rounded-top-3">
                <h5 class="modal-title" id="modalSuppressionLabel">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i> Confirmation de Suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p>Êtes-vous sûr de vouloir supprimer définitivement la randonnée : <br><strong
                        id="randonneeTitre"></strong> ?</p>
                <div class="alert alert-warning small mt-3">
                    Cette action est irréversible. La randonnée, ses circuits et son événement de calendrier seront
                    supprimés.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <a id="btnConfirmerSuppression" href="#" class="btn btn-danger rounded-pill">
                    <i class="fa-solid fa-trash-can me-2"></i> Confirmer la suppression
                </a>
            </div>
        </div>
    </div>
</div>
<template id="circuit-template">
    <div class="circuit-card card border-success mb-4" data-index="INDEX">
        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
            <h5 class="mb-0 card-title-circuit">
                <i class="fa-solid fa-route me-1"></i> Circuit n°<span class="circuit-number">INDEX_PLUS_1</span>
                <span class="badge bg-light text-dark ms-2 circuit-principal-badge"
                    style="display: none;">Principal</span>
            </h5>
            <button type="button" class="btn btn-sm btn-outline-light btn-remove-circuit" title="Supprimer ce circuit">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <input type="hidden" name="circuits[INDEX][id]" value="">
            <div class="form-floating mb-4">
                <input type="text" name="circuits[INDEX][nom]" id="nom_circuit_INDEX" class="form-control"
                    placeholder="Nom du circuit (ex: Petit parcours, 15 km)" value="" required>
                <label for="nom_circuit_INDEX">Nom du Circuit</label>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="form-floating">
                        <input type="number" step="0.1" name="circuits[INDEX][distance_km]" id="distance_km_INDEX"
                            class="form-control" placeholder="Distance (km)" value="" required>
                        <label for="distance_km_INDEX"><i class="fa-solid fa-arrows-left-right me-1"></i> Distance
                            (km)</label>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="form-floating">
                        <input type="number" step="0.1" name="circuits[INDEX][duree_heures]" id="duree_heures_INDEX"
                            class="form-control" placeholder="Durée estimée (heures)" value="" required>
                        <label for="duree_heures_INDEX"><i class="fa-solid fa-clock me-1"></i> Durée (heures)</label>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="form-floating">
                        <input type="number" name="circuits[INDEX][denivele_positif]" id="denivele_positif_INDEX"
                            class="form-control" placeholder="Dénivelé positif (mètres)" value="" required>
                        <label for="denivele_positif_INDEX"><i class="fa-solid fa-arrow-up-wide-short me-1"></i>
                            Dénivelé Positif (m)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="number" step="0.01" min="0"
                            name="circuits[INDEX][prix_inscription_moins_18_ans_licencie]"
                            id="prix_inscription_moins_18_ans_licencie_INDEX" class="form-control"
                            placeholder="Prix d'inscription (ex: 15.00)"
                            value="<?= htmlspecialchars($prixInscriptionMoins18AnsLicencie) ?>" required>
                        <label for="prix_inscription_moins_18_ans_licencie_INDEX"><i
                                class="fa-solid fa-euro-sign me-1"></i> Prix
                            d'inscription pour les moins de 18 ans licencié</label>
                    </div>
                    <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                        un point '.' pour les décimales.</small>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="number" step="0.01" min="0"
                            name="circuits[INDEX][prix_inscription_moins_18_ans_non_licencie]"
                            id="prix_inscription_moins_18_ans_non_licencie_INDEX" class="form-control"
                            placeholder="Prix d'inscription (ex: 15.00)"
                            value="<?= htmlspecialchars($prixInscriptionMoins18AnsNonLicencie) ?>" required>
                        <label for="prix_inscription_moins_18_ans_non_licencie_INDEX"><i
                                class="fa-solid fa-euro-sign me-1"></i> Prix
                            d'inscription pour les moins de 18 ans non licencié</label>
                    </div>
                    <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                        un point '.' pour les décimales.</small>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="number" step="0.01" min="0"
                            name="circuits[INDEX][prix_inscription_adulte_licencie]"
                            id="prix_inscription_adulte_licencie_INDEX" class="form-control"
                            placeholder="Prix d'inscription (ex: 15.00)"
                            value="<?= htmlspecialchars($prixInscriptionAdulteLicencie) ?>" required>
                        <label for="prix_inscription_adulte_licencie_INDEX"><i class="fa-solid fa-euro-sign me-1"></i>
                            Prix
                            d'inscription pour les adultes licencié</label>
                    </div>
                    <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                        un point '.' pour les décimales.</small>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="number" step="0.01" min="0"
                            name="circuits[INDEX][prix_inscription_adulte_non_licencie]"
                            id="prix_inscription_adulte_non_licencie_INDEX" class="form-control"
                            placeholder="Prix d'inscription (ex: 15.00)"
                            value="<?= htmlspecialchars($prixInscriptionAdulteNonLicencie) ?>" required>
                        <label for="prix_inscription_adulte_non_licencie_INDEX"><i
                                class="fa-solid fa-euro-sign me-1"></i> Prix
                            d'inscription pour les adultes licencié</label>
                    </div>
                    <small class="text-muted">Prix en euros pour l'inscription. Utilisez
                        un point '.' pour les décimales.</small>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <select name="circuits[INDEX][type]" id="type_INDEX" class="form-select">
                            <option value="VTT" <?= ($type === 'VTT') ? 'selected' : '' ?>>
                                VTT</option>
                            <option value="GRAVEL" <?= ($type === 'GRAVEL') ? 'selected' : '' ?>>GRAVEL</option>
                            <option value="ROUTE" <?= ($type === 'ROUTE') ? 'selected' : '' ?>>ROUTE</option>
                            <option value="PÉDESTRE" <?= ($type === 'PÉDESTRE') ? 'selected' : '' ?>>PÉDESTRE</option>
                        </select>
                        <label for="type_INDEX">
                            <i class="fa-solid fa-tags me-1"></i> Type
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <select name="circuits[INDEX][difficulte]" id="niveau_difficulte_INDEX" class="form-select">
                            <option value="Facile" <?= ($diff === 'Facile') ? 'selected' : '' ?>>Facile</option>
                            <option value="Modéré" <?= ($diff === 'Modéré') ? 'selected' : '' ?>>Modéré</option>
                            <option value="Difficile" <?= ($diff === 'Difficile') ? 'selected' : '' ?>>Difficile</option>
                            <option value="Très Difficile" <?= ($diff === 'Très Difficile') ? 'selected' : '' ?>>Très
                                Difficile</option>
                        </select>
                        <label for="niveau_difficulte_INDEX">
                            <i class="fa-solid fa-gauge-high me-1"></i> Niveau de
                            difficulté
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="file" name="circuits[INDEX][fichier_gpx]" id="fichier_gpx_INDEX"
                            class="form-control" placeholder="Fichier GPX" value="">
                        <label for="fichier_gpx_INDEX"><i class="fa-solid fa-file-export me-1"></i> Fichier GPX
                            (Optionnel)</label>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input is-principal-toggle" type="radio" role="switch"
                            name="circuits_est_principal" id="est_principal_circuit_INDEX" value="INDEX" required>
                        <label class="form-check-label" for="est_principal_circuit_INDEX">Définir comme circuit
                            Principal
                            (sera affiché par défaut)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalSuppression = document.getElementById('modalSuppression');
        if (modalSuppression) {
            modalSuppression.addEventListener('show.bs.modal', function (event) {
                // Bouton qui a déclenché la modale
                const button = event.relatedTarget;

                // Extraction des infos data-*
                const url = button.getAttribute('data-url');
                const titreRandonnee = button.getAttribute('data-titre-randonnee');

                // Mise à jour du contenu de la modale
                const modalTitleElement = modalSuppression.querySelector('#randonneeTitre');
                const modalConfirmButton = modalSuppression.querySelector('#btnConfirmerSuppression');

                modalTitleElement.textContent = titreRandonnee;
                modalConfirmButton.href = url; // Met à jour l'URL du bouton de confirmation
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('circuits-container');
        const template = document.getElementById('circuit-template');
        const addButton = document.getElementById('add-circuit-btn');
        const nbCircuitsIndicator = document.getElementById('nb-circuits-indicator');

        // Fonction pour mettre à jour les numéros, les indices (names/ids) et les labels
        function updateCircuitIndices() {
            const circuitCards = container.querySelectorAll('.circuit-card');

            circuitCards.forEach((card, index) => {
                const oldIndex = card.dataset.index;
                const newIndex = index;

                // Mise à jour de l'attribut data-index de la carte
                card.dataset.index = newIndex;

                // Mise à jour du numéro visible
                card.querySelector('.circuit-number').textContent = newIndex + 1;

                // Remplacement des indices dans les attributs name, id et for
                card.querySelectorAll('[name*="' + oldIndex + '"], [id*="' + oldIndex + '"], [for*="' + oldIndex + '"]').forEach(element => {
                    ['name', 'id', 'for'].forEach(attr => {
                        if (element.hasAttribute(attr)) {
                            let value = element.getAttribute(attr);
                            // Assurez-vous de ne remplacer que l'indice du tableau (e.g., [0]) et les suffixes (e.g., _0)
                            value = value.replace(new RegExp('\\[' + oldIndex + '\\]', 'g'), '[' + newIndex + ']');
                            value = value.replace(new RegExp('_' + oldIndex, 'g'), '_' + newIndex);
                            element.setAttribute(attr, value);

                            // Cas spécial pour la valeur du radio bouton "Principal"
                            if (element.classList.contains('is-principal-toggle') && element.getAttribute('name') === 'circuits_est_principal') {
                                element.setAttribute('value', newIndex);
                            }
                        }
                    });
                });
            });

            // Mise à jour de l'indicateur du nombre de circuits dans l'onglet
            nbCircuitsIndicator.textContent = circuitCards.length;

            // Gérer le bouton de suppression (toujours désactivé si seul circuit restant)
            circuitCards.forEach(card => {
                const removeBtn = card.querySelector('.btn-remove-circuit');
                if (removeBtn) {
                    removeBtn.style.display = (circuitCards.length > 1) ? 'block' : 'none';
                }
            });
        }

        // Gestion du circuit principal au chargement
        function updatePrincipalIndicator() {
            container.querySelectorAll('.circuit-card').forEach(card => {
                const radio = card.querySelector('.is-principal-toggle');
                const badge = card.querySelector('.circuit-principal-badge');

                if (radio && badge) {
                    if (radio.checked) {
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
        }

        // --- Événements ---

        // 1. Ajouter un circuit
        addButton.addEventListener('click', function () {
            let circuitCards = container.querySelectorAll('.circuit-card');
            const nextIndex = circuitCards.length;

            // Cloner le template
            const newCircuit = template.content.cloneNode(true).firstElementChild;

            // Remplacer les placeholders 'INDEX' par le nouvel indice
            let html = newCircuit.outerHTML;
            html = html.replace(/INDEX_PLUS_1/g, nextIndex + 1);
            html = html.replace(/INDEX/g, nextIndex);

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const finalCircuit = tempDiv.firstElementChild;

            container.appendChild(finalCircuit);

            updateCircuitIndices();
        });

        // 2. Supprimer un circuit ou changer le circuit principal
        container.addEventListener('click', function (e) {

            // Suppression
            if (e.target.closest('.btn-remove-circuit')) {
                const btn = e.target.closest('.btn-remove-circuit');
                const cardToRemove = btn.closest('.circuit-card');

                if (container.querySelectorAll('.circuit-card').length > 1) {
                    const radioPrincipal = cardToRemove.querySelector('.is-principal-toggle');
                    const wasPrincipal = radioPrincipal.checked;

                    cardToRemove.remove();

                    // Si on a supprimé le circuit principal, on met le premier comme principal
                    if (wasPrincipal) {
                        const firstCard = container.querySelector('.circuit-card');
                        if (firstCard) {
                            const firstRadio = firstCard.querySelector('.is-principal-toggle');
                            if (firstRadio) {
                                firstRadio.checked = true;
                            }
                        }
                    }

                    updateCircuitIndices();
                    updatePrincipalIndicator(); // Mise à jour après la suppression et l'éventuel changement de principal

                } else {
                    alert("Vous devez avoir au moins un circuit pour la randonnée.");
                }
            }

            // Changement de circuit principal
            if (e.target.closest('.is-principal-toggle')) {
                updatePrincipalIndicator();
            }
        });

        // Initialisation au chargement de la page (pour les circuits chargés en PHP)
        updateCircuitIndices(); // Assurez-vous que les indices sont bien mis à jour si on est en mode édition
        updatePrincipalIndicator();
    });
</script>