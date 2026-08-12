<?php
// Chemin du fichier GPX (ajuster la logique d'accès à la BDD si besoin)
$gpxFilePath = $event->getGpxFilePath();
// Optionnel : Si BDD ne contient que le nom du fichier
// if (!empty($gpxFilePath) && !str_starts_with($gpxFilePath, '/uploads/gpx/')) {
//     $gpxFilePath = '/uploads/gpx/' . $gpxFilePath;
// }
$mapId = 'gpx-map-' . $event->getId();
?>
<div class="compte-rendu-section py-5">

    <div class="container">
        <!-- En-tête globale -->
        <div class="text-center mb-5">
            <p class="badge-titre text-uppercase fw-semibold mb-2">
                <i class="bi bi-journal-text me-2"></i><?= $page->getNom() ?> | Compte Rendu
            </p>
            <h1 class="display-4 fw-bold text-gradient mb-3"><?= htmlspecialchars($event->getTitre()) ?></h1>
            <p class="date text-light fs-5">
                <i class="bi bi-calendar3 me-2"></i>
                <?= $event->getDateStart() ? $event->getDateStart()->format('d/m/Y H:i') : '' ?>
                <?= $event->getDateEnd() ? ' - ' . $event->getDateEnd()->format('d/m/Y H:i') : '' ?>
            </p>
            <div class="underline mx-auto mt-3"></div>
        </div>

        <!-- Contenu principal -->
        <div class="content px-md-5 px-3 py-4">
            <div class="content-inner fs-5 lh-lg text-light">
                <?= $event->getCompteRendu() ?>
            </div>
        </div>
        <?php if (!empty($gpxFilePath)): ?>

            <div style="position: relative;" id="gpx">

                <!-- htmlspecialchars($gpxFilePath, ENT_QUOTES, 'UTF-8') -->
                <a href="" download class="btn btn-primary disabled" style="
                position: absolute; 
                top: 10px; 
                right: 10px; 
                z-index: 1000; /* Assure qu'il est au-dessus de la carte */
                background-color: #007bff; 
                color: white; 
                padding: 8px 12px; 
                border-radius: 20px; 
                text-decoration: none;
                font-size: 14px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.3);
           ">
                    <i class="fas fa-download"></i> Télécharger le GPX
                </a>
                <div id="<?= $mapId ?>"
                    style="height: 500px; margin-top: 15px; border-radius: 20px; border: 1px solid #ddd;">
                </div>
            </div>
            <script>
                // NOTE: Le code JavaScript de la carte reste inchangé.
                document.addEventListener('DOMContentLoaded', function () {
                    const map = L.map('<?= $mapId ?>');

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    new L.GPX("<?= htmlspecialchars($gpxFilePath, ENT_QUOTES, 'UTF-8') ?>", {
                        async: true,
                        polyline_options: { color: 'red', opacity: 0.7, weight: 3 },
                        marker_options: {
                            startIconUrl: 'https://raw.githubusercontent.com/mpetroff/leaflet-gpx/master/dist/pin-icon-start.png',
                            endIconUrl: 'https://raw.githubusercontent.com/mpetroff/leaflet-gpx/master/dist/pin-icon-end.png',
                            shadowUrl: 'https://raw.githubusercontent.com/mpetroff/leaflet-gpx/master/dist/pin-shadow.png'
                        }
                    }).on('loaded', function (e) {
                        map.fitBounds(e.target.getBounds());
                    }).addTo(map);
                });
            </script>

        <?php endif; ?>

        <!-- Bouton retour -->
        <div class="text-center mt-5">
            <a href="/page/<?= $page->getUrl() ?>"
                class="btn btn-lg btn-outline-light px-5 py-2 rounded-pill retour-btn">
                <i class="bi bi-arrow-left-circle me-2"></i> Retour sur la page
            </a>
        </div>
    </div>
</div>