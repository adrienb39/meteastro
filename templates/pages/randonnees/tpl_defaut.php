<?php
/**
 * Modèle de Page par défaut (tpl_defaut) pour l'affichage d'une Randonnée.
 * * Variables requises :
 * - $randonnee (Entité Randonnee) - Assurez-vous qu'elle n'est pas NULL avant d'inclure ce template.
 * - $circuitsData (Array des données des circuits)
 * - self::UPLOAD_DIR (Constante pour le chemin d'upload)
 */

$r = $randonnee; // Alias pour simplifier l'écriture
$mainColor = htmlspecialchars($r->getCouleurThematique() ?? '#4D8495');

// Préparation des données pour le JS pour une meilleure robustesse de la date
$dateRandonnee = $r->getDateRandonnee();
// Récupération des composants de la date pour le constructeur JS Date(année, mois, jour, heure, minute, seconde)
$jsYear = $dateRandonnee->format('Y');
$jsMonth = $dateRandonnee->format('n') - 1; // Mois JS est base 0 (Jan=0)
$jsDay = $dateRandonnee->format('j');
$jsHour = $dateRandonnee->format('G'); // Heure sans zéro initial
$jsMinute = $dateRandonnee->format('i');
$jsSecond = $dateRandonnee->format('s');

// ✅ CARTE : Préparation du lieu de départ pour l'URL et l'iframe
$lieuDepart = htmlspecialchars($r->getLieuDepart());
$lieuDepartEncoded = urlencode($r->getLieuDepart());

// URL Google Maps Embed (pour l'iframe)
// ATTENTION: J'ai retiré les domaines non standard pour une URL Google Maps valide
$mapIframeSrc = "https://maps.google.com/maps?q={$lieuDepartEncoded}&t=&z=14&ie=UTF8&iwloc=&output=embed";

// URL Google Maps pour le bouton (lien direct)
$mapButtonHref = "https://maps.google.com/?q={$lieuDepartEncoded}";

// ✅ INSCRIPTION : Définir si les inscriptions sont FERMÉES (basé sur le statut PHP)
// Cette variable PHP est maintenue pour le statut initial, mais le JS gère la désactivation en temps réel.
$statutInscription = strtolower($r->getStatutInscription());
$isClosed = ($statutInscription === 'fermé');
$linkClass = $isClosed ? ' disabled-link' : ''; // Conserve la classe CSS pour la désactivation initiale
?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($_SESSION['error_message']); ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']); ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="container-fluid content-section-page py-5">
    <div class="container content" style="font-family: 'Poppins', sans-serif;">

        <section class="mb-5">

            <header class="d-flex justify-content-center flex-column align-items-center mb-5">
                <h1 class="text-center mb-3 display-3 fw-bold">
                    <span style="color: <?= $mainColor; ?>;">
                        <?= htmlspecialchars($r->getTitre()); ?><br>
                        <small
                            class="text-light fs-4 fw-normal"><?= htmlspecialchars(mb_ucfirst(implode(' ', array_replace($d = explode(' ', (new IntlDateFormatter('fr_FR', 1, -1))->format($r->getDateRandonnee())), [1 => mb_ucfirst($d[1] ?? '', 'UTF-8')])), 'UTF-8')); ?></small>
                    </span>
                </h1>
                <h2 class="text-center mb-4 fs-4 fw-light">
                    <span
                        style="color: <?= $mainColor; ?>; background-color: rgb(239,254,149); padding: 5px 10px; border-radius: 5px;">
                        <?= nl2br(htmlspecialchars($r->getDescriptionCourte())); ?>
                    </span>
                </h2>
                <p class="text-light">Départ :
                    <strong><?= $lieuDepart; ?></strong> à
                    <?= htmlspecialchars($r->getDateRandonnee()->format('H:i')); ?>
                </p>
            </header>

            <?php if ($r->getImagePrincipale()): ?>
                <div class="mb-5">
                    <figure class="border border-black rounded-3 overflow-hidden shadow">
                        <img style="width: 100%; height: auto; object-fit: cover;"
                            src="<?= htmlspecialchars(self::UPLOAD_DIR . $r->getImagePrincipale()); ?>"
                            alt="Image principale de <?= htmlspecialchars($r->getTitre()); ?>" class="img-fluid rounded-3">
                    </figure>
                </div>
            <?php endif; ?>

            <?php if ($r->getDescriptionComplete()): ?>
                <div class="mb-5 p-3 border-start border-5" style="border-color: <?= $mainColor; ?> !important;">
                    <?= nl2br($r->getDescriptionComplete()); ?>
                </div>
            <?php endif; ?>

            <?php if ($r->isEstAnnulee()): ?>
                <div class="alert alert-danger mb-5" role="alert">
                    <h3 class="alert-heading">⚠️ Événement Annulé</h3>
                    <p><?= htmlspecialchars($r->getMessageAnnulation() ?? "Cet événement a été annulé."); ?></p>
                </div>
            <?php endif; ?>
        </section>

        <hr class="hr my-5">

        <section class="mb-5 text-center">
            <h3 class="fw-bold mb-4 fs-3 text-uppercase" style="color: <?= $mainColor; ?>;">📍 Carte et Lieu de Départ
            </h3>
            <p class="fs-5">Rendez-vous à : <strong><?= $lieuDepart; ?></strong></p>

            <div class="border border-secondary rounded-3 shadow-lg my-4 overflow-hidden"
                style="height: 400px; width: 100%;">
                <iframe width="100%" height="100%" frameborder="0" style="border:0;" src="<?= $mapIframeSrc; ?>"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <a class="btn btn-lg fw-bold" style="background-color: rgb(239,254,149); color: #FF4040;"
                href="<?= $mapButtonHref; ?>" target="_blank">
                Ouvrir dans Google Maps
            </a>
        </section>

        <hr class="hr my-5">

        <section class="mb-5">
            <h3 class="fw-bold mb-4 fs-3 text-uppercase text-center" style="color: <?= $mainColor; ?>;">✅ Inscriptions
            </h3>

            <div class="row justify-content-center align-items-center">

                <div class="col-lg-6 d-flex flex-column align-items-center mb-4">
                    <h4 class="fw-bold mb-3 fs-5 text-uppercase">Compte à Rebours</h4>

                    <?php
                    // --- Message initial basé sur le statut PHP ---
                    if ($isClosed):
                        ?>
                        <p id="info_inscription" class="fw-bold text-center">
                            Inscriptions en ligne terminées. <strong class="text-danger">Inscription possible sur place
                                (majoration de 2€).</strong>
                        </p>
                    <?php else: ?>
                        <p id="info_inscription" class="fw-bold text-center"></p>
                    <?php endif; ?>

                    <div class="box-rebours d-flex justify-content-center shadow rounded p-3" id="rebours"
                        style="min-width: 300px; background-color: #f8f9fa;">

                        <?php if ($isClosed): ?>
                            <span class='fs-4 fw-bold text-danger'>Inscriptions Terminées</span>
                        <?php else: ?>
                            <div class="box_jour d-flex flex-column align-items-center mx-2">
                                <div id="jour" class="fs-1 fw-bold text-dark">00</div><span id="jour_label"
                                    class="small">Jours</span>
                            </div>

                            <span class="fs-1 fw-bold text-muted d-flex align-items-center pt-3 me-2">:</span>

                            <div class="box_heure d-flex flex-column align-items-center mx-2">
                                <div id="heure" class="fs-1 fw-bold text-dark">00</div><span id="heure_label"
                                    class="small">Heures</span>
                            </div>

                            <span class="fs-1 fw-bold text-muted d-flex align-items-center pt-3 me-2">:</span>

                            <div class="box_minute d-flex flex-column align-items-center mx-2">
                                <div id="minute" class="fs-1 fw-bold text-dark">00</div><span id="minute_label"
                                    class="small">Minutes</span>
                            </div>

                            <span class="fs-1 fw-bold text-muted d-flex align-items-center pt-3 me-2">:</span>

                            <div class="box_seconde d-flex flex-column align-items-center mx-2">
                                <div id="seconde" class="fs-1 fw-bold text-dark">00</div><span id="seconde_label"
                                    class="small">Secondes</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <div class="mb-4" id="statut-alert-container">
                        <?php if ($statutInscription === 'Ouvert'): ?>
                            <div class="alert alert-success fw-bold fs-5" id="statut-alert">Les inscriptions en ligne sont
                                actuellement
                                **OUVERTES** !</div>
                        <?php elseif ($isClosed): ?>
                            <div class="alert alert-danger fw-bold fs-5" id="statut-alert">Les inscriptions en ligne sont
                                actuellement
                                **FERMÉES**.<br>Veuillez consulter les modalités d'inscription sur place.</div>
                        <?php else: ?>
                            <div class="alert alert-info" id="statut-alert">Veuillez choisir votre parcours ci-dessous pour
                                vous inscrire en
                                ligne.</div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center" id="inscription-buttons">
                        <button type="button" class="btn btn-lg mb-3 mx-2 fw-bold<?= $linkClass; ?>"
                            style="background: <?= $mainColor; ?>; color: white; opacity: <?= $isClosed ? '0.6' : '1'; ?>;"
                            data-bs-toggle="modal" data-bs-target="#modalCircuitsVTT" <?= $isClosed ? 'disabled' : ''; ?>>
                            Inscription VTT
                        </button>
                        <button type="button" class="btn btn-lg mb-3 mx-2 fw-bold<?= $linkClass; ?>"
                            style="background: <?= $mainColor; ?>; color: white; opacity: <?= $isClosed ? '0.6' : '1'; ?>;"
                            data-bs-toggle="modal" data-bs-target="#modalCircuitsGravel" <?= $isClosed ? 'disabled' : ''; ?>>
                            Inscription GRAVEL
                        </button>
                        <button type="button" class="btn btn-lg mb-3 mx-2 fw-bold<?= $linkClass; ?>"
                            style="background: <?= $mainColor; ?>; color: white; opacity: <?= $isClosed ? '0.6' : '1'; ?>;"
                            data-bs-toggle="modal" data-bs-target="#modalCircuitsRoute" <?= $isClosed ? 'disabled' : ''; ?>>
                            Inscription ROUTE
                        </button>
                        <button type="button" class="btn btn-lg mb-3 mx-2 fw-bold<?= $linkClass; ?>"
                            style="background: <?= $mainColor; ?>; color: white; opacity: <?= $isClosed ? '0.6' : '1'; ?>;"
                            data-bs-toggle="modal" data-bs-target="#modalCircuitsPedestre" <?= $isClosed ? 'disabled' : ''; ?>>
                            Inscription PÉDESTRE
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <hr class="hr my-5">

        <section>
            <h3 class="fw-bold mb-4 fs-3 text-uppercase text-center" style="color: <?= $mainColor; ?>;">🗺️ Détails des
                Parcours</h3>

            <div class="row g-4 justify-content-center text-center mt-3">

                <?php
                $circuitsByType = [];
                foreach ($circuitsData as $circuit) {
                    // La logique de classification en PHP (la même que vous aviez)
                    $nomUpper = strtoupper($circuit['type']);

                    if (str_contains($nomUpper, 'VTT')) {
                        $type = 'VTT';
                    } elseif (str_contains($nomUpper, 'ROUTE')) {
                        $type = 'ROUTE';
                    } elseif (str_contains($nomUpper, 'GRAVEL')) {
                        $type = 'GRAVEL';
                    } elseif (str_contains($nomUpper, 'PÉDESTRE')) {
                        $type = 'PÉDESTRE';
                    } else {
                        // Optionnel : Gérer les types non catégorisés
                        $type = 'AUTRE';
                    }

                    $circuitsByType[$type][] = $circuit;
                }

                foreach ($circuitsByType as $type => $circuits):

                    $iconClass = 'fa-circle-question';
                    if ($type === 'VTT' || $type === 'GRAVEL') {
                        $iconClass = 'fa-person-biking';
                    } elseif ($type === 'ROUTE') {
                        $iconClass = 'fa-road';
                    } elseif ($type === 'PÉDESTRE') {
                        $iconClass = 'fa-person-hiking';
                    }
                    ?>
                    <div class="col-12 mt-4">

                        <h4 class="mb-4">
                            <i class="fa-solid <?= $iconClass; ?> me-2 fs-5" style="color: <?= $mainColor; ?>;"></i>
                            <strong><?= htmlspecialchars($type); ?></strong>
                        </h4>

                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 justify-content-center">

                            <?php foreach ($circuits as $circuit): ?>
                                <?php
                                // Sécurité : on vérifie que $circuit est bien un objet avant d'appeler getId()
                                $nombreInscrits = 0;

                                if (is_object($circuit)) {
                                    // Si vous avez une relation "inscriptions" dans votre entité Circuit
                                    $nombreInscrits = count($circuit->getId());
                                } elseif (is_array($circuit) && isset($circuit['id'])) {
                                    // Si votre contrôleur envoie des tableaux associatifs au lieu d'objets
                                    // Vous devrez peut-être compter un tableau d'inscriptions séparé ici
                                    $nombreInscrits = isset($circuit['inscriptions']) ? count($circuit['inscriptions']) : 0;
                                }

                                $inscriptionText = $nombreInscrits > 0
                                    ? ' (' . $nombreInscrits . ' inscrit' . ($nombreInscrits > 1 ? 's' : '') . ')'
                                    : '';

                                // Préparation des données
                                $distanceKm = htmlspecialchars($circuit['distance_km'] ?? 'N/A');
                                $denivelePositif = htmlspecialchars($circuit['denivele_positif'] ?? '0');
                                $difficulte = htmlspecialchars($circuit['difficulte'] ?? 'N/A');
                                $lieuDepart = htmlspecialchars($circuit['lieu_depart'] ?? $r->getLieuDepart());
                                $nbRavitaillements = $circuit['nb_ravitaillements'] ?? 0;
                                $isPrincipal = $circuit['est_principal'] ?? false;
                                $prixInscriptionMoins18AnsLicencie = $circuit['prix_inscription_moins_18_ans_licencie'];
                                $prixInscriptionMoins18AnsNonLicencie = $circuit['prix_inscription_moins_18_ans_non_licencie'];
                                $prixInscriptionAdulteLicencie = $circuit['prix_inscription_adulte_licencie'];
                                $prixInscriptionAdulteNonLicencie = $circuit['prix_inscription_adulte_non_licencie'];
                                ?>

                                <div class="col d-flex justify-content-center">
                                    <div class="card rounded-3 mb-3 shadow-sm w-100"
                                        style="border-color: <?= $mainColor; ?>; max-width: 300px;">
                                        <div class="card-body rounded-3 p-2"
                                            style="background-color: <?= $mainColor; ?>; color: white;">
                                            <strong class="fs-5"><?= $distanceKm; ?> km</strong>
                                            <span class="d-block fs-6"><?= $inscriptionText; ?></span>
                                            <?php if ($isPrincipal): ?>
                                                <span class="badge bg-warning text-dark mt-1">Principal</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer text-start small text-dark p-2">
                                            <p class="m-0"><i class="fa-solid fa-location-dot me-1"></i> Départ :
                                                <strong><?= $lieuDepart; ?></strong>
                                            </p>
                                            <p class="m-0"><i class="fa-solid fa-mountain me-1"></i> Dénivelé :
                                                <strong><?= $denivelePositif; ?>
                                                    m</strong>
                                            </p>
                                            <p class="m-0"><i class="fa-solid fa-star me-1"></i> Difficulté :
                                                <strong><?= $difficulte; ?></strong>
                                            </p>
                                            <?php if ($nbRavitaillements > 0): ?>
                                                <p class="m-0"><i class="fa-solid fa-apple-whole me-1"></i> Ravitaillement :
                                                    <strong><?= htmlspecialchars($nbRavitaillements); ?></strong>
                                                </p>
                                            <?php endif; ?>
                                            <p class="m-0"><i class="fa-solid fa-money-bill me-1"></i> Tarif moins de 18 ans
                                                licencié :
                                                <strong><?= $prixInscriptionMoins18AnsLicencie; ?>€</strong>
                                            </p>
                                            <p class="m-0"><i class="fa-solid fa-money-bill me-1"></i> Tarif moins de 18 ans non
                                                licencié :
                                                <strong><?= $prixInscriptionMoins18AnsNonLicencie; ?>€</strong>
                                            </p>
                                            <p class="m-0"><i class="fa-solid fa-money-bill me-1"></i> Tarif adulte licencié :
                                                <strong><?= $prixInscriptionAdulteLicencie; ?>€</strong>
                                            </p>
                                            <p class="m-0"><i class="fa-solid fa-money-bill me-1"></i> Tarif adulte non licencié
                                                :
                                                <strong><?= $prixInscriptionAdulteNonLicencie; ?>€</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <!-- <div class="d-flex justify-content-center mt-5">
                <a class="btn btn-lg fw-bold" style="background: <?= $mainColor; ?>; color: white;" href="#"
                    target="_blank">
                    Télécharger tous les fichiers GPX
                </a>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <a class="btn btn-lg mb-3 mx-2 fw-bold" style="background: <?= $mainColor; ?>; color: white;" href="#">
                    Voir les Sponsors
                </a>
                <a class="btn btn-lg mb-3 mx-2 fw-bold" style="background: <?= $mainColor; ?>; color: white;" href="#">
                    Télécharger l'Affiche (PDF)
                </a>
            </div> -->

        </section>
    </div>
</div>
<div class="modal fade" id="modalCircuitsVTT" tabindex="-1" aria-labelledby="modalCircuitsVTTLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: <?= $mainColor; ?>; color: white;">
                <h5 class="modal-title fw-bold" id="modalCircuitsVTTLabel">
                    <i class="fa-solid fa-person-biking me-2"></i> Choix du Circuit VTT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <?php
                $circuitsVTT = $circuitsByType['VTT'] ?? [];

                if (empty($circuitsVTT)):
                    ?>
                    <div class="alert alert-info text-center">
                        Aucun circuit VTT n'est actuellement disponible pour l'inscription.
                    </div>
                <?php else: ?>
                    <p class="text-center mb-4">
                        Sélectionnez le circuit VTT sur lequel vous souhaitez vous inscrire.
                    </p>
                    <div class="row g-3 justify-content-center">
                        <?php foreach ($circuitsVTT as $circuit): ?>
                            <?php
                            $distanceKm = htmlspecialchars($circuit['distance_km'] ?? 'N/A');
                            $denivelePositif = htmlspecialchars($circuit['denivele_positif'] ?? '0');
                            $slugRandonnee = $r->getSlug();

                            // Création du lien d'inscription unique pour ce circuit
                            // Il faudra que votre contrôleur gère /inscription/pedestre/{circuit_id}
                            $inscriptionUrl = "/randonnee/{$slugRandonnee}/inscription/vtt/{$circuit['id']}";
                            ?>
                            <div class="col-sm-6 col-md-4 d-flex">
                                <a href="<?= $inscriptionUrl; ?>" class="card text-decoration-none shadow w-100"
                                    style="border-color: <?= $mainColor; ?>;">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title fw-bold text-uppercase" style="color: <?= $mainColor; ?>;">
                                            Circuit VTT
                                        </h6>
                                        <div class="display-6 fw-bold text-dark mb-2">
                                            <?= $distanceKm; ?> <small class="fs-6">km</small>
                                        </div>
                                        <p class="card-text m-0 small text-muted">
                                            <i class="fa-solid fa-mountain me-1"></i> Dénivelé:
                                            <?= $denivelePositif; ?> m
                                        </p>
                                    </div>
                                    <div class="card-footer text-center p-2"
                                        style="background-color: <?= $mainColor; ?>; color: white;">
                                        S'inscrire
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCircuitsGravel" tabindex="-1" aria-labelledby="modalCircuitsGravelLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: <?= $mainColor; ?>; color: white;">
                <h5 class="modal-title fw-bold" id="modalCircuitsGravelLabel">
                    <i class="fa-solid fa-person-biking me-2"></i> Choix du Circuit GRAVEL
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <?php
                $circuitsGravel = $circuitsByType['GRAVEL'] ?? [];

                if (empty($circuitsGravel)):
                    ?>
                    <div class="alert alert-info text-center">
                        Aucun circuit GRAVEL n'est actuellement disponible pour l'inscription.
                    </div>
                <?php else: ?>
                    <p class="text-center mb-4">
                        Sélectionnez le circuit gravel sur lequel vous souhaitez vous inscrire.
                    </p>
                    <div class="row g-3 justify-content-center">
                        <?php foreach ($circuitsGravel as $circuit): ?>
                            <?php
                            $distanceKm = htmlspecialchars($circuit['distance_km'] ?? 'N/A');
                            $denivelePositif = htmlspecialchars($circuit['denivele_positif'] ?? '0');
                            $slugRandonnee = $r->getSlug();

                            // Création du lien d'inscription unique pour ce circuit
                            // Il faudra que votre contrôleur gère /inscription/pedestre/{circuit_id}
                            $inscriptionUrl = "/randonnee/{$slugRandonnee}/inscription/gravel/{$circuit['id']}";
                            ?>
                            <div class="col-sm-6 col-md-4 d-flex">
                                <a href="<?= $inscriptionUrl; ?>" class="card text-decoration-none shadow w-100"
                                    style="border-color: <?= $mainColor; ?>;">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title fw-bold text-uppercase" style="color: <?= $mainColor; ?>;">
                                            Circuit GRAVEL
                                        </h6>
                                        <div class="display-6 fw-bold text-dark mb-2">
                                            <?= $distanceKm; ?> <small class="fs-6">km</small>
                                        </div>
                                        <p class="card-text m-0 small text-muted">
                                            <i class="fa-solid fa-mountain me-1"></i> Dénivelé:
                                            <?= $denivelePositif; ?> m
                                        </p>
                                    </div>
                                    <div class="card-footer text-center p-2"
                                        style="background-color: <?= $mainColor; ?>; color: white;">
                                        S'inscrire
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCircuitsRoute" tabindex="-1" aria-labelledby="modalCircuitsRouteLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: <?= $mainColor; ?>; color: white;">
                <h5 class="modal-title fw-bold" id="modalCircuitsRouteLabel">
                    <i class="fa-solid fa-road me-2"></i> Choix du Circuit ROUTE
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <?php
                // Assurez-vous que la variable $circuitsByType est disponible ici.
                // Si elle n'est pas disponible, vous devez la recalculer ou la déplacer.
                // Dans ce contexte, elle est calculée dans la section Détails des Parcours, 
                // mais si le modal est appelé avant, déplacez le bloc de calcul PHP en haut du fichier.
                
                $circuitsRoute = $circuitsByType['ROUTE'] ?? [];

                if (empty($circuitsRoute)):
                    ?>
                    <div class="alert alert-info text-center">
                        Aucun circuit ROUTE n'est actuellement disponible pour l'inscription.
                    </div>
                <?php else: ?>
                    <p class="text-center mb-4">
                        Sélectionnez le circuit ROUTE sur lequel vous souhaitez vous inscrire.
                    </p>
                    <div class="row g-3 justify-content-center">
                        <?php foreach ($circuitsRoute as $circuit): ?>
                            <?php
                            $distanceKm = htmlspecialchars($circuit['distance_km'] ?? 'N/A');
                            $denivelePositif = htmlspecialchars($circuit['denivele_positif'] ?? '0');
                            $slugRandonnee = $r->getSlug(); // Le slug de la randonnée
                    
                            // Création du lien d'inscription unique pour ce circuit
                            // Il faudra que votre contrôleur gère /inscription/route/{circuit_id}
                            $inscriptionUrl = "/randonnee/{$slugRandonnee}/inscription/route/{$circuit['id']}";
                            ?>
                            <div class="col-sm-6 col-md-4 d-flex">
                                <a href="<?= $inscriptionUrl; ?>" class="card text-decoration-none shadow w-100"
                                    style="border-color: <?= $mainColor; ?>;">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title fw-bold text-uppercase" style="color: <?= $mainColor; ?>;">
                                            Circuit Route
                                        </h6>
                                        <div class="display-6 fw-bold text-dark mb-2">
                                            <?= $distanceKm; ?> <small class="fs-6">km</small>
                                        </div>
                                        <p class="card-text m-0 small text-muted">
                                            <i class="fa-solid fa-mountain me-1"></i> Dénivelé:
                                            <?= $denivelePositif; ?> m
                                        </p>
                                    </div>
                                    <div class="card-footer text-center p-2"
                                        style="background-color: <?= $mainColor; ?>; color: white;">
                                        S'inscrire
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCircuitsPedestre" tabindex="-1" aria-labelledby="modalCircuitsPedestreLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: <?= $mainColor; ?>; color: white;">
                <h5 class="modal-title fw-bold" id="modalCircuitsPedestreLabel">
                    <i class="fa-solid fa-person-hiking me-2"></i> Choix du Circuit PÉDESTRE
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <?php
                $circuitsPedestre = $circuitsByType['PÉDESTRE'] ?? [];

                if (empty($circuitsPedestre)):
                    ?>
                    <div class="alert alert-info text-center">
                        Aucun circuit PÉDESTRE n'est actuellement disponible pour l'inscription.
                    </div>
                <?php else: ?>
                    <p class="text-center mb-4">
                        Sélectionnez le circuit PÉDESTRE sur lequel vous souhaitez vous inscrire.
                    </p>
                    <div class="row g-3 justify-content-center">
                        <?php foreach ($circuitsPedestre as $circuit): ?>
                            <?php
                            $distanceKm = htmlspecialchars($circuit['distance_km'] ?? 'N/A');
                            $denivelePositif = htmlspecialchars($circuit['denivele_positif'] ?? '0');
                            $slugRandonnee = $r->getSlug();

                            // Création du lien d'inscription unique pour ce circuit
                            // Il faudra que votre contrôleur gère /inscription/pedestre/{circuit_id}
                            $inscriptionUrl = "/randonnee/{$slugRandonnee}/inscription/pedestre/{$circuit['id']}";
                            ?>
                            <div class="col-sm-6 col-md-4 d-flex">
                                <a href="<?= $inscriptionUrl; ?>" class="card text-decoration-none shadow w-100"
                                    style="border-color: <?= $mainColor; ?>;">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title fw-bold text-uppercase" style="color: <?= $mainColor; ?>;">
                                            Circuit Pédestre
                                        </h6>
                                        <div class="display-6 fw-bold text-dark mb-2">
                                            <?= $distanceKm; ?> <small class="fs-6">km</small>
                                        </div>
                                        <p class="card-text m-0 small text-muted">
                                            <i class="fa-solid fa-mountain me-1"></i> Dénivelé:
                                            <?= $denivelePositif; ?> m
                                        </p>
                                    </div>
                                    <div class="card-footer text-center p-2"
                                        style="background-color: <?= $mainColor; ?>; color: white;">
                                        S'inscrire
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- 1. FONCTIONS D'AIDE ---

        /** Ajoute un zéro devant les nombres < 10. */
        const caractere = (nb) => (nb < 10) ? '0' + nb : nb.toString();

        /** Fonction de libellé pour le compte à rebours. */
        const genre = (nb, libelle) => libelle;

        /** Calcule la couleur de fond pour le clignotement en fonction du temps restant (en secondes). */
        const calculerCouleur = (total_secondes) => {
            const seuil_rouge = 3600 * 24;       // < 1 jour
            const seuil_orange = 3600 * 96;      // < 4 jours
            const seuil_jaune = 3600 * 24 * 14;  // < 14 jours

            if (total_secondes < seuil_rouge) {
                return "red";
            } else if (total_secondes < seuil_orange) {
                return "orange";
            } else if (total_secondes < seuil_jaune) {
                return "yellow";
            } else {
                return "";
            }
        };

        // --- 2. CONFIGURATION ET ÉLÉMENTS DU DOM ---

        // ** Récupération des composants de date depuis PHP **
        const year = <?= $jsYear; ?>;
        const month = <?= $jsMonth; ?>;
        const day = <?= $jsDay; ?>;
        const hour = <?= $jsHour; ?>;
        const minute = <?= $jsMinute; ?>;
        const second = <?= $jsSecond; ?>;

        // Date de l'événement (Heure de départ/fin de l'événement)
        const eventDate = new Date(year, month, day, hour, minute, second).getTime();

        // Date de fin des inscriptions en ligne (7 jours avant l'événement)
        const daysBeforeEvent = 7;
        const inscriptionEndDate = eventDate - (daysBeforeEvent * 24 * 60 * 60 * 1000);

        // Récupération des éléments DOM (Variables globales)
        let jourEl = document.getElementById("jour");
        let heureEl = document.getElementById("heure");
        let minuteEl = document.getElementById("minute");
        let secondeEl = document.getElementById("seconde");
        let jourLabelEl = document.getElementById("jour_label");
        let heureLabelEl = document.getElementById("heure_label");
        let minuteLabelEl = document.getElementById("minute_label");
        let secondeLabelEl = document.getElementById("seconde_label");

        const reboursEl = document.getElementById("rebours");
        const infoEl = document.getElementById("info_inscription");
        const statutAlertEl = document.getElementById("statut-alert");
        const mainColor = "<?= $mainColor; ?>";
        const inscriptionButtonContainer = document.getElementById('inscription-buttons');
        const isClosedInitial = <?= $isClosed ? 'true' : 'false'; ?>;

        // Message de fin (pour plus de lisibilité, on le définit ici)
        const finalMessageHTML = `
            <div class="d-flex flex-column align-items-center">
                <span class='fs-4 fw-bold text-danger'>Inscriptions en ligne Terminées</span>
            </div>
        `;
        const finalInfoText = `Inscriptions en ligne terminées. <strong class="text-danger">Inscription possible sur place (majoration de 2€).</strong>`;
        const finalAlertHTML = `<div class="alert alert-danger fw-bold fs-5">Les inscriptions en ligne sont actuellement **FERMÉES**.<br>Veuillez consulter les modalités d'inscription sur place.</div>`;


        // Vérification critique des éléments DOM et de la date
        if (isNaN(eventDate) || !reboursEl || !infoEl || !inscriptionButtonContainer) {
            console.error("Erreur critique : Le compte à rebours est désactivé. Vérifiez le format de date PHP ou les IDs DOM.");
            return;
        }

        // Si PHP a déjà marqué comme fermé, on ne lance pas la boucle de compte à rebours
        if (isClosedInitial) {
            toggleInscriptionLinks(true);
            return;
        }

        /**
         * Applique la couleur spécifiée aux chiffres et aux labels du compte à rebours.
         * @param {string} color - La couleur (ex: 'black', 'white').
         */
        const setTextColor = (color) => {
            // Vérifie si les éléments du compte à rebours existent
            const currentJourEl = document.getElementById("jour");
            if (!currentJourEl) return;

            const currentElements = [currentJourEl, document.getElementById("heure"), document.getElementById("minute"), document.getElementById("seconde")];
            const currentLabels = [document.getElementById("jour_label"), document.getElementById("heure_label"), document.getElementById("minute_label"), document.getElementById("seconde_label")];

            // Les ELEMENTS (chiffres)
            currentElements.forEach(el => {
                if (el) {
                    el.style.color = color; // Applique 'black' ou 'white'
                    // Enlève la classe Bootstrap pour utiliser style.color
                    el.classList.remove('text-dark');
                }
            });

            // Les LABELS (Jours, Heures...)
            currentLabels.forEach(el => {
                if (el) {
                    el.style.color = color; // Applique 'black' ou 'white'
                    // Enlève la classe Bootstrap pour utiliser style.color
                    el.classList.remove('text-dark');
                }
            });
        }

        // Fonction pour désactiver/activer les liens d'inscription
        const toggleInscriptionLinks = (disable = false) => {
            const links = inscriptionButtonContainer.querySelectorAll('a');
            links.forEach(link => {
                link.classList.toggle('disabled-link', disable);
                link.style.opacity = disable ? '0.6' : '1';
            });
        };


        // --- 3. LOGIQUE DU COMPTE À REBOURS PRINCIPAL ---

        let interval;

        const updateCountdown = () => {
            const now = new Date().getTime();
            const distanceEvent = eventDate - now;
            const distanceInscription = inscriptionEndDate - now;

            // CAS A: Événement terminé
            if (distanceEvent <= 0) {
                clearInterval(interval);
                reboursEl.innerHTML = `<span class='fs-3 fw-bold' style='color: ${mainColor}'>Événement terminé</span>`;
                infoEl.innerHTML = "";
                const statutAlertContainer = document.getElementById('statut-alert-container');
                if (statutAlertContainer) statutAlertContainer.innerHTML = `<div class="alert alert-info fw-bold fs-5">L'événement a déjà eu lieu.</div>`;
                toggleInscriptionLinks(true);
                return;
            }

            // CAS B: Compte à rebours terminé (Inscriptions fermées)
            if (distanceInscription <= 0) {
                clearInterval(interval);
                reboursEl.innerHTML = finalMessageHTML;
                reboursEl.style.backgroundColor = ""; // Réinitialiser le clignotement
                infoEl.innerHTML = finalInfoText;
                const statutAlertContainer = document.getElementById('statut-alert-container');
                if (statutAlertContainer) {
                    statutAlertContainer.innerHTML = finalAlertHTML;
                }
                toggleInscriptionLinks(true);
                return;
            }

            // CAS C: Inscriptions Ouvertes (Compte à rebours normal)
            const distanceToDisplay = distanceInscription;
            const targetText = 'Fin des inscriptions en ligne dans : ';
            infoEl.textContent = targetText; // Mise à jour du texte d'information

            // --- CALCUL ET AFFICHAGE ---
            const total_secondes = distanceToDisplay / 1000;

            const days = Math.floor(total_secondes / (60 * 60 * 24));
            const hours = Math.floor((total_secondes % (60 * 60 * 24)) / (60 * 60));
            const minutes = Math.floor((total_secondes % (60 * 60)) / 60);
            const seconds = Math.floor(total_secondes % 60);

            // Mise à jour des chiffres 
            if (jourEl) jourEl.innerText = caractere(days);
            if (heureEl) heureEl.innerText = caractere(hours);
            if (minuteEl) minuteEl.innerText = caractere(minutes);
            if (secondeEl) secondeEl.innerText = caractere(seconds);

            // Mise à jour des libellés
            if (jourLabelEl) jourLabelEl.textContent = genre(days, 'Jours');
            if (heureLabelEl) heureLabelEl.textContent = genre(hours, 'Heures');
            if (minuteLabelEl) minuteLabelEl.textContent = genre(minutes, 'Minutes');
            if (secondeLabelEl) secondeLabelEl.textContent = genre(seconds, 'Secondes');

            // --- GESTION DU CLIGNOTEMENT et COULEUR DU TEXTE ---
            const couleur = calculerCouleur(total_secondes);

            if (couleur) {
                // Effet de clignotement
                if (reboursEl.style.backgroundColor === couleur) {
                    reboursEl.style.backgroundColor = "";
                    setTextColor('white');
                } else {
                    reboursEl.style.backgroundColor = couleur;
                    setTextColor('black');
                }
            } else {
                reboursEl.style.backgroundColor = "";
                setTextColor('white');
            }

            // S'assurer que les liens sont actifs
            toggleInscriptionLinks(false);
        };

        // Démarrage du compte à rebours si les inscriptions ne sont pas déjà fermées par PHP
        if (!isClosedInitial) {
            updateCountdown();
            interval = setInterval(updateCountdown, 1000);
        }
    });
</script>