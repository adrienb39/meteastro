<div class="container py-5">

        <?php
        /**
         * --- 1. CONFIGURATION ET LOGIQUE DE TRAITEMENT (PHP) ---
         */
        $cacheFile = __DIR__ . '/prochains_lancements.json';
        $cacheTime = 900; // 15 minutes
        $launches = [];

        $apiResponse = null;

        // Gestion du cache local
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
            $apiResponse = file_get_contents($cacheFile);
        } else {
            $apiUrl = "https://ll.thespacedevs.com/2.2.0/launch/upcoming/?limit=5";
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'Meteastro/1.0 (admin@meteastro.fr)',
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($response)) {
                $apiResponse = $response;
                file_put_contents($cacheFile, $apiResponse);
            } elseif (file_exists($cacheFile)) {
                $apiResponse = file_get_contents($cacheFile);
            }
        }

        // Décodage et sécurisation des données JSON
        if ($apiResponse) {
            $launchData = json_decode($apiResponse, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $launches = $launchData['results'] ?? [];
            }
        }
        ?>

        <?php /** --- 2. RENDU VISUEL (HTML / BOOTSTRAP) --- */ ?>
        <?php if (!empty($launches)): ?>
            <div id="carouselLaunches" class="carousel slide mb-5" data-bs-ride="carousel" style="position: relative;">

                <div class="carousel-indicators" style="bottom: -35px;">
                    <?php foreach ($launches as $index => $launch): ?>
                        <button type="button" data-bs-target="#carouselLaunches" data-bs-slide-to="<?= $index ?>"
                            class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                            aria-label="Slide <?= $index + 1 ?>" style="background-color: #60a5fa;">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">
                    <?php foreach ($launches as $index => $launch):
                        // Extraction et sécurisation des variables
                        $agencyName = $launch['launch_service_provider']['name'] ?? 'Agence inconnue';
                        $missionDesc = $launch['mission']['description'] ?? 'Aucune description disponible pour cette mission.';
                        $statusName = $launch['status']['name'] ?? 'Planifié';
                        $statusId = $launch['status']['id'] ?? 1;
                        $launchPad = $launch['pad']['location']['name'] ?? 'Lieu inconnu';
                        $imageUrl = $launch['image'] ?? null;

                        // Traitement des dates
                        $hasNetDate = !empty($launch['net']);
                        $dateTextUTC = $hasNetDate ? gmdate("d M Y H:i", strtotime($launch['net'])) : 'Date inconnue';
                        $isoCountdown = $hasNetDate ? date('c', strtotime($launch['net'])) : null;

                        // Extraction du lien de streaming direct
                        $liveUrl = null;
                        if (!empty($launch['vidURLs']) && is_array($launch['vidURLs'])) {
                            $liveUrl = $launch['vidURLs'][0]['url'] ?? null;
                        }
                        ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-bs-interval="8000">
                            <div class="row align-items-center px-4 px-md-5 mx-md-3">

                                <div class="col-lg-8 order-2 order-lg-1">
                                    <div class="p-4 mb-4 shadow"
                                        style="background: var(--glass, rgba(15, 23, 42, 0.6)) !important; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid var(--border, rgba(59, 130, 246, 0.3)) !important; border-radius: 1.5rem !important; transition: all 0.4s ease;">

                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="status-indicator pulse mb-0"
                                                    style="position: relative; display: inline-block;"></span>
                                                <h2 class="orbitron-sub m-0"
                                                    style="font-size: 1.1rem; letter-spacing: 2px; color: #60a5fa;">
                                                    <?= $index === 0 ? 'PROCHAIN DÉCOLLAGE MONDIAL' : 'LANCEMENT À VENIR N°' . ($index + 1) ?>
                                                </h2>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <span class="badge text-uppercase text-white"
                                                    style="font-size: 0.75rem; background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4);">
                                                    <i class="fas fa-space-shuttle me-1"></i>
                                                    <?= htmlspecialchars($agencyName) ?>
                                                </span>
                                                <span class="badge"
                                                    style="font-size: 0.75rem; background-color: <?= ($statusId === 3) ? '#22c55e' : '#3b82f6'; ?>;">
                                                    <?= htmlspecialchars($statusName) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="glitch-wrapper mb-3">
                                            <div class="glitch-text" data-text="<?= htmlspecialchars($launch['name']) ?>"
                                                style="font-weight: 700; font-size: 1.5rem; color: #fff;">
                                                <?= htmlspecialchars($launch['name']) ?>
                                            </div>
                                        </div>

                                        <p class="text-white small opacity-75 mb-3"
                                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                            <?= htmlspecialchars($missionDesc) ?>
                                        </p>

                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                            <?php if ($isoCountdown): ?>
                                                <div class="countdown-wrapper d-flex gap-2 text-center"
                                                    data-countdown="<?= $isoCountdown ?>"
                                                    style="font-family: monospace; font-size: 1.1rem; color: #a3e635; background: rgba(0,0,0,0.3); padding: 8px 15px; border-radius: 6px; width: fit-content; border: 1px dashed rgba(163, 230, 53, 0.4);">
                                                    <i class="fas fa-clock align-self-center me-1" style="color: #60a5fa;"></i>
                                                    <span class="countdown-timer">Calcul...</span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($liveUrl): ?>
                                                <a href="<?= htmlspecialchars($liveUrl) ?>" target="_blank"
                                                    class="btn btn-sm btn-danger d-inline-flex align-items-center gap-2 pulse-live-btn"
                                                    style="border-radius: 6px; padding: 9px 15px; font-weight: bold; background: #ef4444; border: none; box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);">
                                                    <i class="fas fa-video"></i> DIRECT
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                        <div class="location-tag m-0 small" style="color: #60a5fa; opacity: 0.95;">
                                            <i class="fas fa-map-marker-alt" style="color: #a3e635;"></i>
                                            <span><?= htmlspecialchars($launchPad) ?></span>
                                            <span class="coord-divider mx-1" style="color: rgba(59, 130, 246, 0.3);">|</span>
                                            <i class="fas fa-calendar-alt me-1" style="color: #60a5fa;"></i>
                                            <span class="launch-date-text text-white">
                                                <?= htmlspecialchars($dateTextUTC) ?> UTC
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 order-1 order-lg-2 mb-4 mb-lg-0 text-center">
                                    <?php if ($imageUrl): ?>
                                        <img class="glass-card shadow mb-4" src="<?= htmlspecialchars($imageUrl); ?>"
                                            alt="<?= htmlspecialchars($launch['name']) ?>"
                                            style="width: 100%; max-width: 350px; height: 220px; object-fit: cover; border-radius: 1rem; border: 1px solid rgba(59, 130, 246, 0.3); box-shadow: 0 0 20px rgba(59, 130, 246, 0.15);">
                                    <?php else: ?>
                                        <div class="glass-card shadow d-flex align-items-center justify-content-center mx-auto mb-4"
                                            style="width: 100%; max-width: 350px; height: 220px; border-radius: 1rem; border: 1px solid rgba(59, 130, 246, 0.2); background: rgba(59, 130, 246, 0.05); box-shadow: 0 0 15px rgba(59, 130, 246, 0.05);">
                                            <i class="fas fa-rocket fa-3x" style="color: rgba(96, 165, 250, 0.4);"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselLaunches" data-bs-slide="prev"
                    style="width: 5%; min-width: 30px;">
                    <span class="carousel-control-prev-icon" aria-hidden="true"
                        style="filter: drop-shadow(0px 0px 6px #60a5fa) brightness(1.2);"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselLaunches" data-bs-slide="next"
                    style="width: 5%; min-width: 30px;">
                    <span class="carousel-control-next-icon" aria-hidden="true"
                        style="filter: drop-shadow(0px 0px 6px #60a5fa) brightness(1.2);"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>

            </div>

            <style>
                @keyframes pulseButton {
                    0% {
                        transform: scale(1);
                        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5);
                    }

                    70% {
                        transform: scale(1.03);
                        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
                    }

                    100% {
                        transform: scale(1);
                        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
                    }
                }

                .pulse-live-btn {
                    animation: pulseButton 2s infinite;
                }
            </style>

            <?php /** --- 3. DYNAMISME CÔTÉ NAVIGATEUR (JAVASCRIPT) --- */ ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const countdownElements = document.querySelectorAll("[data-countdown]");

                    // Horloge de compte à rebours dynamique
                    function updateCountdowns() {
                        const now = new Date().getTime();

                        countdownElements.forEach(container => {
                            const targetDate = new Date(container.getAttribute("data-countdown")).getTime();
                            const timerDisplay = container.querySelector(".countdown-timer");

                            if (isNaN(targetDate)) return;

                            const distance = targetDate - now;

                            if (distance < 0) {
                                timerDisplay.innerHTML = "<span style='color: #ef4444; font-weight: bold;'>VOL EN COURS / TERMINÉ</span>";
                                return;
                            }

                            // Calcul précis du temps restant
                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            let text = "";
                            if (days > 0) text += days + "j ";
                            text += String(hours).padStart(2, '0') + "h ";
                            text += String(minutes).padStart(2, '0') + "m ";
                            text += String(seconds).padStart(2, '0') + "s";

                            timerDisplay.innerText = text;
                        });
                    }

                    // Initier immédiatement et mettre à jour toutes les secondes
                    updateCountdowns();
                    setInterval(updateCountdowns, 1000);
                });
            </script>
        <?php endif; ?>

        <!--  -->

        <div class="row justify-content-center pt-5" id="contacts">
            <div class="col-md-10 col-xl-8">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold text-light">🪐 CONTACT 🪐</h2>
                    <p class="text-secondary">Envoyez une transmission directe à l'administrateur</p>
                </div>

                <form id="contactForm" class="glass-card p-4 p-md-5">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Nom & Prénom</label>
                            <input type="text" name="pseudo" class="form-control" placeholder="Nom Prénom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Coordonnées
                                Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@cosmos.com"
                                required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Message</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Votre transmission..."
                            required></textarea>
                    </div>
                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-astro w-100 py-3 fw-bold">
                        ENVOYER LE SIGNAL
                    </button>
                </form>
            </div>
        </div>
    </div>