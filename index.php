<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/__partials/db.class.php';

$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    $obj = new Db();
    $userData = $obj->query2("SELECT `newsletter` FROM `users` WHERE `id_users` = ?", [$userId]);

    // Si l'utilisateur existe et que le champ 'newsletter' vaut stricte NULL
    if (!empty($userData) && is_null($userData[0]['newsletter'])) {
        header("Location: /connexion/welcome-newsletter.php");
        exit(); // Bloque la suite de l'exécution d'index.php
    }
}
?>
<?php
session_start();
require_once __DIR__ . '/__partials/db.class.php';

// Initialisation DB
$obj = new Db();

// Vérification de la connexion
$userId = $_SESSION['user_id'] ?? null;
$isConnected = !empty($userId);

// Redirection si l'utilisateur tente de soumettre sans être connecté
if (!$isConnected && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: connexion/login.php");
    exit();
}

// --- TRAITEMENT DES FORMULAIRES ---
if ($isConnected && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        // 1. MISE À JOUR DU PROFIL
        if (isset($_POST['update_profile'])) {
            $newName = trim($_POST['name'] ?? '');
            $newEmail = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $newPass = $_POST['password'] ?? '';

            if (!empty($newName) && $newEmail) {
                if (!empty($newPass)) {
                    $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
                    $sql = "UPDATE `users` SET `name` = ?, `email` = ?, `password` = ? WHERE `id_users` = ?";
                    $obj->query2($sql, [$newName, $newEmail, $hashedPass, $userId]);
                } else {
                    $sql = "UPDATE `users` SET `name` = ?, `email` = ? WHERE `id_users` = ?";
                    $obj->query2($sql, [$newName, $newEmail, $userId]);
                }

                $_SESSION = array();
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
                session_destroy();

                header("Location: connexion/login.php?updated=1");
                exit();
            }
        }

        // 2. MISE À JOUR DE LA NEWSLETTER
        if (isset($_POST['update_newsletter'])) {
            $newsletter = isset($_POST['newsletter']) ? 1 : 0;

            $sql = "UPDATE `users` SET `newsletter` = ? WHERE `id_users` = ?";
            $obj->query2($sql, [$newsletter, $userId]);

            $_SESSION['newsletter'] = $newsletter;

            header("Location: " . $_SERVER['PHP_SELF'] . "?saved=1");
            exit();
        }

    } catch (Throwable $e) {
        // En cas d'erreur, affiche le détail au lieu d'un écran noir
        die("Erreur survenue lors du traitement : " . $e->getMessage());
    }
}

// --- RÉCUPÉRATION DES DONNÉES EN BDD POUR AFFICHAGE ---
if ($isConnected) {
    $userData = $obj->query2("SELECT `name`, `email`, `newsletter` FROM `users` WHERE `id_users` = ?", [$userId]);
    if (!empty($userData)) {
        $userName = $userData[0]['name'];
        $userEmail = $userData[0]['email'];
        $userNewsletter = (int) $userData[0]['newsletter'];

        // Synchronisation des variables de session
        $_SESSION['name'] = $userName;
        $_SESSION['email'] = $userEmail;
        $_SESSION['newsletter'] = $userNewsletter;
    }
} else {
    $userName = '';
    $userEmail = '';
    $userNewsletter = 0;
}

// Informations de version du site
$version = require __DIR__ . '/__partials/version.php';
$siteVersion = $version['siteVersion'];
$appVersion = $siteVersion . '.' . $version['appBuild'];
$updated = $version['updated'];
$dateAffichee = "{$updated['day']} {$updated['num']} {$updated['month']} {$updated['year']}";
?>
<?php
session_start();
require_once "config/controllerUserData.php";
$db = createPdoConnection();

/**
 * Récupère la liste des articles vérifiés pour une catégorie (table) spécifique.
 *
 * @param PDO $db Instance de la connexion à la base de données.
 * @param string $table Nom de la table cible ('astronomie' ou 'meteorologie').
 * @param int $limit Nombre maximal d'articles à récupérer (par défaut 5).
 * @return array Liste des articles (un tableau vide [] est retourné en cas d'erreur ou d'absence de données).
 */
function getLatestNews(PDO $db, string $table, int $limit = 5): array
{
    // 1. Sécurisation stricte contre les injections SQL sur le nom de la table (Whitelisting)
    $allowed_tables = ['astronomie', 'meteorologie'];

    if (!in_array($table, $allowed_tables, true)) {
        // Optionnel : vous pouvez lever une exception ou simplement logger l'erreur ici
        error_log("Tentative d'accès à une table non autorisée : " + $table);
        return [];
    }

    try {
        // 2. Préparation de la requête avec une limite pour ne pas charger le carrousel inutilement
        $sql = "SELECT * FROM `$table` WHERE `verified` = 'y' ORDER BY `id` DESC LIMIT :limit";
        $stmt = $db->prepare($sql);

        // On force le paramètre LIMIT à être interprété comme un entier (INT)
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        // 3. Récupération des résultats sous forme de tableau
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results ?: [];

    } catch (PDOException $e) {
        // 4. Enregistrement de l'erreur dans les logs du serveur plutôt que de la masquer
        error_log("Erreur SQL dans getLatestNews() pour la table '$table' : " . $e->getMessage());
        return [];
    }
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro | Astronomie & Météorologie</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#007bff">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --glass: rgba(15, 23, 42, 0.7);
            --border: rgba(255, 255, 255, 0.1);
            --accent: #3b82f6;
        }

        body {
            background-color: #05070a;
            color: #e2e8f0;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        /* Background étoilé */
        #star-field {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at center, #0a0f1a 0%, #05070a 100%);
        }

        /* Style Glassmorphism Bootstrap */
        .glass-card {
            background: var(--glass) !important;
            backdrop-filter: blur(12px);
            border: 1px solid var(--border) !important;
            border-radius: 1.5rem !important;
            transition: all 0.4s ease;
        }

        .glass-card:hover {
            border-color: var(--accent) !important;
            transform: translateY(-5px);
        }

        /* Boites de catégories */
        .category-box {
            position: relative;
            height: 350px;
            overflow: hidden;
            border-radius: 1.5rem;
            display: block;
            text-decoration: none;
        }

        .category-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
            filter: brightness(0.5);
        }

        .category-box:hover img {
            transform: scale(1.1);
            filter: brightness(0.7);
        }

        /* Onglets personnalisés */
        .nav-tabs {
            border: none;
        }

        .nav-link {
            color: #94a3b8 !important;
            border: none !important;
            font-family: 'Space Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            padding: 1rem 1.5rem;
        }

        .nav-link.active {
            background: none !important;
            color: var(--accent) !important;
            border-bottom: 2px solid var(--accent) !important;
        }

        /* Inputs de formulaire */
        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid var(--border) !important;
            color: white !important;
            border-radius: 0.75rem;
            padding: 0.8rem 1.2rem;
        }

        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 0.25 margin-top: 10px;
            rem rgba(59, 130, 246, 0.25);
        }

        .btn-astro {
            background: var(--accent);
            border: none;
            border-radius: 0.75rem;
            padding: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-astro:hover {
            background: #2563eb;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        p {
            color: white;
        }
    </style>
</head>

<body>

    <div id="notification-onboarding"
        style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 20px; border-radius: 12px; z-index: 999999; font-family: sans-serif;">
        <h3 style="margin-top: 0; color: #333;">Activer les notifications ? 🚀</h3>
        <p style="color: #666; font-size: 14px;">Restez informé en temps réel des nouveautés en astronomie et
            météorologie directement sur votre appareil.</p>
        <div style="text-align: right; margin-top: 15px;">
            <button id="btn-refuse-notif"
                style="background: none; border: none; color: #999; margin-right: 15px; cursor: pointer; font-weight: bold;">Plus
                tard</button>
            <button id="btn-accept-notif"
                style="background: #007bff; border: none; color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">Autoriser</button>
        </div>
    </div>

    <div id="star-field"></div>
    <div class="sun"></div>
    <div class="lens-flare"></div>

    <?php include "__partials/menu.php"; ?>

    <div class="container py-5">

        <!-- <div class="text-center mb-5">
            <span class="badge rounded-pill px-4 py-2" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa;">
                ⚡ Gestimag v1.0-rc1 disponible (2024)
            </span>
        </div> -->

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

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <a href="/divers/astronomie/astronomie.php" class="category-box glass-card shadow">
                    <img src="ressources/IMG_0191.JPG" alt="Astronomie">
                    <div class="position-absolute bottom-0 start-0 p-4 w-100 bg-gradient">
                        <h2 class="h1 fw-bold text-white mb-1">Astronomie</h2>
                        <p class="text-light-50 mb-0 opacity-75">Explorez les étoiles et le cosmos.</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-6">
                <a href="/divers/meteorologie/meteorologie.php" class="category-box glass-card shadow">
                    <img src="ressources/IMG_0933.jpg" alt="Météorologie">
                    <div class="position-absolute bottom-0 start-0 p-4 w-100 bg-gradient">
                        <h2 class="h1 fw-bold text-white mb-1">Météorologie</h2>
                        <p class="text-light-50 mb-0 opacity-75">Données climatiques en temps réel.</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-md-10 col-xl-8">
                <div class="glass-card shadow-lg">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="fw-bold mb-4 d-flex align-items-center">
                            <span class="bg-primary rounded-pill me-3" style="width: 5px; height: 30px;"></span>
                            Flux de données
                        </h3>

                        <ul class="nav nav-tabs mb-4" id="newsTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="astro-tab" data-bs-toggle="tab"
                                    data-bs-target="#astro-news" type="button" role="tab">Astronomie</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="meteo-tab" data-bs-toggle="tab"
                                    data-bs-target="#meteo-news" type="button" role="tab">Météorologie</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="newsTabsContent">

                            <div class="tab-pane fade show active" id="astro-news" role="tabpanel">
                                <?php
                                $astro_data = getLatestNews($db, 'astronomie');
                                $astro_list = (is_array($astro_data) && !isset($astro_data['title_contenu'])) ? $astro_data : ($astro_data ? [$astro_data] : []);
                                ?>

                                <?php if (!empty($astro_list) && is_array($astro_list)): ?>
                                    <div id="carouselAstro" class="carousel slide" data-bs-ride="carousel">

                                        <?php if (count($astro_list) > 1): ?>
                                            <div class="carousel-indicators" style="margin-bottom: -1.5rem;">
                                                <?php $i = 0; ?>
                                                <?php foreach ($astro_list as $astro): ?>
                                                    <?php if (is_array($astro)): ?>
                                                        <button type="button" data-bs-target="#carouselAstro"
                                                            data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : ''; ?>"
                                                            aria-current="<?= $i === 0 ? 'true' : 'false'; ?>"
                                                            aria-label="Slide <?= $i + 1 ?>"
                                                            style="background-color: rgba(255, 255, 255, 0.3); border: none; height: 4px; transition: all 0.2s ease-in-out;"
                                                            onmouseover="if(!this.classList.contains('active')) this.style.backgroundColor='rgba(255, 255, 255, 0.7)'"
                                                            onmouseout="if(!this.classList.contains('active')) this.style.backgroundColor='rgba(255, 255, 255, 0.3)'">
                                                        </button>
                                                        <?php $i++; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="carousel-inner">
                                            <?php $count = 0; ?>
                                            <?php foreach ($astro_list as $astro): ?>
                                                <?php if (is_array($astro)): ?>
                                                    <div class="carousel-item <?= $count === 0 ? 'active' : ''; ?>"
                                                        data-bs-interval="5000">
                                                        <div class="row align-items-center px-5">
                                                            <div class="col-md-8">
                                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                                    <?php if ($count === 0): ?>
                                                                        <span class="badge bg-success text-uppercase fw-bold px-2 py-1"
                                                                            style="font-size: 0.65rem; letter-spacing: 0.5px;">Dernier
                                                                            signal</span>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <h4 class="h5 fw-bold text-white">
                                                                    <?= $astro['title_contenu'] ?? 'Sans titre' ?>
                                                                </h4>
                                                                <p class="text-light">
                                                                    <?= substr($astro['contenu'] ?? '', 0, 160) ?>...
                                                                </p>
                                                                <a href="/divers/astronomie/contenu-astronomie.php?id=<?= $astro['id'] ?>"
                                                                    class="btn btn-link p-0 text-primary text-decoration-none fw-bold">
                                                                    DÉCODER LA SUITE →
                                                                </a>
                                                            </div>
                                                            <?php if (!empty($astro['filename'])): ?>
                                                                <div class="col-md-4 mt-3 mt-md-0">
                                                                    <img src="../../uploads/<?= htmlspecialchars($astro['filename']); ?>"
                                                                        alt=""
                                                                        style="width: 100%; height: auto; max-height: 150px; object-fit: cover; border-radius: 4px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <?php $count++; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if ($count > 1): ?>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselAstro"
                                                data-bs-slide="prev" style="width: 5%; min-width: 40px;">
                                                <span class="d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.15); transition: all 0.2s ease-in-out;"
                                                    onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'; this.style.transform='scale(1.1)';"
                                                    onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.15)'; this.style.transform='scale(1)';">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"
                                                        style="filter: invert(0); width: 1.25rem; height: 1.25rem;"></span>
                                                </span>
                                                <span class="visually-hidden">Précédent</span>
                                            </button>

                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselAstro"
                                                data-bs-slide="next" style="width: 5%; min-width: 40px;">
                                                <span class="d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.15); transition: all 0.2s ease-in-out;"
                                                    onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'; this.style.transform='scale(1.1)';"
                                                    onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.15)'; this.style.transform='scale(1)';">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"
                                                        style="filter: invert(0); width: 1.25rem; height: 1.25rem;"></span>
                                                </span>
                                                <span class="visually-hidden">Suivant</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted fst-italic">Signal non détecté.</p>
                                <?php endif; ?>
                            </div>


                            <div class="tab-pane fade" id="meteo-news" role="tabpanel">
                                <?php
                                $meteo_data = getLatestNews($db, 'meteorologie');
                                $meteo_list = (is_array($meteo_data) && !isset($meteo_data['title_contenu'])) ? $meteo_data : ($meteo_data ? [$meteo_data] : []);
                                ?>

                                <?php if (!empty($meteo_list) && is_array($meteo_list)): ?>
                                    <div id="carouselMeteo" class="carousel slide" data-bs-ride="carousel">

                                        <?php if (count($meteo_list) > 1): ?>
                                            <div class="carousel-indicators" style="margin-bottom: -1.5rem;">
                                                <?php $i = 0; ?>
                                                <?php foreach ($meteo_list as $meteo): ?>
                                                    <?php if (is_array($meteo)): ?>
                                                        <button type="button" data-bs-target="#carouselMeteo"
                                                            data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : ''; ?>"
                                                            aria-current="<?= $i === 0 ? 'true' : 'false'; ?>"
                                                            aria-label="Slide <?= $i + 1 ?>"
                                                            style="background-color: rgba(255, 255, 255, 0.3); border: none; height: 4px; transition: all 0.2s ease-in-out;"
                                                            onmouseover="if(!this.classList.contains('active')) this.style.backgroundColor='rgba(255, 255, 255, 0.7)'"
                                                            onmouseout="if(!this.classList.contains('active')) this.style.backgroundColor='rgba(255, 255, 255, 0.3)'">
                                                        </button>
                                                        <?php $i++; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="carousel-inner">
                                            <?php $count = 0; ?>
                                            <?php foreach ($meteo_list as $meteo): ?>
                                                <?php if (is_array($meteo)): ?>
                                                    <div class="carousel-item <?= $count === 0 ? 'active' : ''; ?>"
                                                        data-bs-interval="5000">
                                                        <div class="row align-items-center px-5">
                                                            <div class="col-md-8">
                                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                                    <?php if ($count === 0): ?>
                                                                        <span class="badge bg-success text-uppercase fw-bold px-2 py-1"
                                                                            style="font-size: 0.65rem; letter-spacing: 0.5px;">Dernier
                                                                            signal</span>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <h4 class="h5 fw-bold text-white">
                                                                    <?= $meteo['title_contenu'] ?? 'Sans titre' ?>
                                                                </h4>
                                                                <p class="text-light">
                                                                    <?= substr($meteo['contenu'] ?? '', 0, 160) ?>...
                                                                </p>
                                                                <a href="/divers/meteorologie/contenu-meteorologie.php?id=<?= $meteo['id'] ?>"
                                                                    class="btn btn-link p-0 text-primary text-decoration-none fw-bold">
                                                                    DÉCODER LA SUITE →
                                                                </a>
                                                            </div>
                                                            <?php if (!empty($meteo['filename'])): ?>
                                                                <div class="col-md-4 mt-3 mt-md-0">
                                                                    <img src="../../uploads/<?= htmlspecialchars($meteo['filename']); ?>"
                                                                        alt=""
                                                                        style="width: 100%; height: auto; max-height: 150px; object-fit: cover; border-radius: 4px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <?php $count++; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if ($count > 1): ?>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselMeteo"
                                                data-bs-slide="prev" style="width: 5%; min-width: 40px;">
                                                <span class="d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.15); transition: all 0.2s ease-in-out;"
                                                    onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'; this.style.transform='scale(1.1)';"
                                                    onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.15)'; this.style.transform='scale(1)';">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"
                                                        style="filter: invert(0); width: 1.25rem; height: 1.25rem;"></span>
                                                </span>
                                                <span class="visually-hidden">Précédent</span>
                                            </button>

                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselMeteo"
                                                data-bs-slide="next" style="width: 5%; min-width: 40px;">
                                                <span class="d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.15); transition: all 0.2s ease-in-out;"
                                                    onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'; this.style.transform='scale(1.1)';"
                                                    onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.15)'; this.style.transform='scale(1)';">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"
                                                        style="filter: invert(0); width: 1.25rem; height: 1.25rem;"></span>
                                                </span>
                                                <span class="visually-hidden">Suivant</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted fst-italic">Signal non détecté.</p>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

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

    <?php include "cookie/cookie.php"; ?>
    <?php include "__partials/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('contactForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const btn = document.getElementById('btnSubmit');
            const originalText = btn.innerHTML; // Utilisation de innerHTML au cas où il y aurait des icônes

            // 1. UI : Verrouillage du bouton (État "Propulsion")
            btn.disabled = true;
            btn.style.opacity = "0.7";
            btn.innerHTML = `<span class="spinner"></span> TRANSMISSION EN COURS...`;

            try {
                const formData = new FormData(form);

                const response = await fetch('contacts.php', {
                    method: 'POST',
                    body: formData,
                    // Optionnel : ajouter un cache 'no-cache' pour éviter des soucis de navigateur
                    cache: 'no-cache'
                });

                // 2. Vérification de la réponse HTTP (404, 500, etc.)
                if (!response.ok) {
                    throw new Error(`Erreur serveur (${response.status})`);
                }

                // 3. Récupération sécurisée du texte pour diagnostiquer le JSON
                const rawText = await response.text();
                let res;

                try {
                    res = JSON.parse(rawText);
                } catch (parseError) {
                    console.error("Réponse brute du serveur :", rawText);
                    throw new Error("La réponse du serveur est corrompue (Format JSON invalide).");
                }

                // 4. Traitement du résultat métier
                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Signal reçu !',
                        text: res.message,
                        background: '#0f172a', // Assorti au thème sombre/astro
                        color: '#f1f5f9',
                        confirmButtonColor: '#38bdf8',
                        timer: 5000
                    });
                    form.reset();
                } else {
                    // Erreur envoyée volontairement par le PHP (ex: champs vides)
                    throw new Error(res.message || 'Le centre de contrôle a rejeté le signal.');
                }

            } catch (err) {
                // 5. Gestion des erreurs (Réseau, JSON corrompu ou erreur PHP)
                console.error("Erreur de transmission :", err);

                Swal.fire({
                    icon: 'error',
                    title: 'Interférence détectée',
                    text: err.message,
                    background: '#0f172a',
                    color: '#f1f5f9',
                    confirmButtonColor: '#ef4444'
                });

            } finally {
                // 6. Restauration du bouton
                btn.disabled = false;
                btn.style.opacity = "1";
                btn.innerHTML = originalText;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /**
 * Générateur d'ambiance Meteastro
 * Gère les étoiles (nuit) et les particules solaires (jour)
 */
        function initSpaceAmbience() {
            const field = document.getElementById('star-field');
            if (!field) return;

            // Nettoyage si le script est relancé
            field.innerHTML = '';

            // Détection du mode via la classe sur le body
            const isLightMode = document.body.classList.contains('lightmode');

            // Configuration selon le mode
            const config = {
                count: isLightMode ? 80 : 150, // Moins de particules le jour pour la clarté
                color: isLightMode ? '#FFD700' : '#FFFFFF', // Or le jour, blanc la nuit
                blur: isLightMode ? '1px' : '0px' // Effet de halo pour la poussière solaire
            };

            const fragment = document.createDocumentFragment();

            for (let i = 0; i < config.count; i++) {
                const particle = document.createElement('div');
                const size = (Math.random() * (isLightMode ? 4 : 2) + 1) + 'px';

                // Styles de base
                Object.assign(particle.style, {
                    position: 'absolute',
                    width: size,
                    height: size,
                    backgroundColor: config.color,
                    left: Math.random() * 100 + '%',
                    top: Math.random() * 100 + '%',
                    borderRadius: '50%',
                    opacity: Math.random() * (isLightMode ? 0.5 : 1),
                    filter: `blur(${config.blur})`,
                    pointerEvents: 'none'
                });

                // Animation spécifique
                if (isLightMode) {
                    // Poussière qui flotte doucement
                    particle.style.animation = `floatSolar ${Math.random() * 15 + 10}s infinite ease-in-out`;
                } else {
                    // Étoiles qui scintillent
                    particle.style.animation = `twinkle ${Math.random() * 3 + 2}s infinite ease-in-out`;
                }

                // Décalage aléatoire pour éviter l'effet "bloc"
                particle.style.animationDelay = Math.random() * 5 + 's';

                fragment.appendChild(particle);
            }

            field.appendChild(fragment);
        }

        // Lancement au chargement
        document.addEventListener('DOMContentLoaded', initSpaceAmbience);
    </script>
    <script>
        (() => {
            // 1. Configuration & Sélection des éléments du DOM
            const DOM = {
                modal: document.getElementById('pwa-component-modal'),
                btnOpen: document.getElementById('pwa-action-open'),
                btnClose: document.getElementById('pwa-action-close'),
                btnInstall: document.getElementById('pwa-action-install'),
                txtStatus: document.getElementById('pwa-text-status'),
                guideIos: document.getElementById('pwa-guide-ios'),
                guideGeneric: document.getElementById('pwa-guide-generic'),
                txtInstructions: document.getElementById('pwa-text-instructions')
            };

            let pwaDeferredPrompt = null;
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

            // 2. Initialisation de l'affichage selon l'OS
            const initPlatformUX = () => {
                if (isIOS) {
                    DOM.txtStatus.textContent = "📱 Configuration : Apple iOS";
                    DOM.guideIos.style.display = "block";
                    DOM.guideGeneric.style.display = "none";
                } else {
                    DOM.txtStatus.textContent = "🤖 Configuration : Android / Standard";
                    DOM.guideIos.style.display = "none";
                    DOM.guideGeneric.style.display = "block";
                    if (DOM.btnInstall) DOM.btnInstall.style.display = 'none';
                }
            };

            // 3. Gestionnaires d'événements pour la Modale
            const toggleModal = (isOpen) => {
                if (!DOM.modal) return;
                DOM.modal.classList.toggle('is-open', isOpen);
            };

            const initModalEvents = () => {
                if (!DOM.btnOpen || !DOM.btnClose) return;

                DOM.btnOpen.addEventListener('click', () => toggleModal(true));
                DOM.btnClose.addEventListener('click', () => toggleModal(false));
                DOM.modal.addEventListener('click', (e) => {
                    if (e.target === DOM.modal) toggleModal(false);
                });
            };

            // 4. Gestion intelligente du défilement (Bouton discret au scroll)
            const initScrollBehavior = () => {
                if (!DOM.btnOpen) return;

                let lastScrollTop = 0;
                window.addEventListener('scroll', () => {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                    // Si l'utilisateur descend de plus de 100px, le bouton s'estompe
                    if (scrollTop > lastScrollTop && scrollTop > 100) {
                        DOM.btnOpen.classList.add('pwa-discret');
                    } else {
                        DOM.btnOpen.classList.remove('pwa-discret');
                    }
                    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                }, { passive: true });
            };

            // 5. Logique PWA (Interception et Déclenchement de l'installation)
            const initPwaEngine = () => {
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    pwaDeferredPrompt = e;

                    // Si on n'est pas sur iOS, on propose le bouton natif à la place des étapes manuelles
                    if (!isIOS && DOM.btnInstall && DOM.txtInstructions) {
                        DOM.btnInstall.style.display = 'block';
                        DOM.txtInstructions.style.display = 'none';
                    }
                });

                if (DOM.btnInstall) {
                    DOM.btnInstall.addEventListener('click', async () => {
                        if (!pwaDeferredPrompt) return;

                        await pwaDeferredPrompt.prompt();
                        const { outcome } = await pwaDeferredPrompt.userChoice;

                        if (outcome === 'accepted') {
                            cleanUpPwaUX();
                        }
                        pwaDeferredPrompt = null;
                    });
                }

                window.addEventListener('appinstalled', () => {
                    cleanUpPwaUX();
                });
            };

            // Nettoyage de l'interface une fois installée
            const cleanUpPwaUX = () => {
                pwaDeferredPrompt = null;
                toggleModal(false);
                if (DOM.btnOpen) DOM.btnOpen.style.display = 'none';
            };

            // Lancement global du script
            const init = () => {
                initPlatformUX();
                initModalEvents();
                initScrollBehavior();
                initPwaEngine();
            };

            document.addEventListener('DOMContentLoaded', init);
        })();
    </script>
    <script src="/js/main.js"></script>
</body>

</html>