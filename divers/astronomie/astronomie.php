<?php
session_start();
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

/**
 * Récupère les articles d'astronomie avec les infos auteurs
 */
function getAstroContent($db)
{
    $sql = "SELECT a.*, u.name 
            FROM astronomie a
            JOIN users u ON a.id_users = u.id_users 
            WHERE a.verified = 'y' 
            ORDER BY a.date_astronomie DESC";
    try {
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$posts = getAstroContent($db);

// $events = [
//     ['title' => 'Éclipse Solaire Totale', 'date' => '2026-08-12 18:00:00', 'type' => 'future'],
//     ['title' => 'Lancement Artemis II', 'date' => '2025-09-15 10:00:00', 'type' => 'future'],
//     ['title' => 'Pluie de Perséides', 'date' => '2026-08-11 22:00:00', 'type' => 'future'],
//     ['title' => 'Atterrissage Perseverance', 'date' => '2021-02-18 21:55:00', 'type' => 'past'],
// ];

// // On filtre pour n'avoir que le futur, et on trie par date la plus proche
// $futureEvents = array_filter($events, function($e) {
//     return strtotime($e['date']) > time();
// });

// usort($futureEvents, function($a, $b) {
//     return strtotime($a['date']) - strtotime($b['date']);
// });

// // On récupère le tout premier (le plus proche)
// $nextEvent = !empty($futureEvents) ? $futureEvents[0] : ['title' => 'Aucun événement', 'date' => ''];

/**
 * RÉCUPÉRATION DES LANCEMENTS SPATIAUX (AVEC SYSTÈME DE CACHE)
 */
// --- CONFIGURATION ---
$cacheFile = __DIR__ . '/prochains_lancements.json';
$cacheTime = 900; // 15 minutes
$launches = [];

// --- 1. RÉCUPÉRATION DES LANCEMENTS (API) ---
$apiResponse = null;

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    $apiResponse = file_get_contents($cacheFile);
} else {
    $apiUrl = "https://ll.thespacedevs.com/2.2.0/launch/upcoming/?limit=5";
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Meteastro/1.0 (admin@meteastro.fr)',
        CURLOPT_TIMEOUT => 8,
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

// Décodage et sécurisation
$launchData = json_decode($apiResponse, true);
if (json_last_error() === JSON_ERROR_NONE) {
    $launches = $launchData['results'] ?? [];
}
$nextLaunch = $launches[0] ?? null;

// --- 2. ÉVÉNEMENTS ASTRONOMIQUES ---
$astroEvents = [
    [
        'date' => '2026-01-01 10:30',
        'title' => 'Pluie des Perséides',
        'desc' => 'Pic d\'activité de la célèbre pluie d\'étoiles filantes.',
        'icon' => 'fa-star-shooting'
    ],
    [
        'date' => '2024-09-18 02:41',
        'title' => 'Éclipse Lunaire Partielle',
        'desc' => 'La Lune traversera partiellement l\'ombre de la Terre.',
        'icon' => 'fa-moon'
    ],
    [
        'date' => '2024-10-02 18:45',
        'title' => 'Éclipse Solaire Annulaire',
        'desc' => 'Le fameux "Cercle de feu" (Pacifique / Am. du Sud).',
        'icon' => 'fa-sun'
    ],
    [
        'date' => '2024-12-14 12:00',
        'title' => 'Pluie des Géminides',
        'desc' => 'L\'un des plus beaux essaims météoritiques de l\'année.',
        'icon' => 'fa-comet'
    ]
];

// --- 3. TRAITEMENT & TRI DES ÉVÉNEMENTS ---

// Filtrer pour ne garder que les événements futurs
$now = time();
$astroEvents = array_filter($astroEvents, function ($e) use ($now) {
    return strtotime($e['date']) > $now;
});

// Trier par date (du plus proche au plus lointain)
usort($astroEvents, function ($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
});

// --- 4. FONCTIONS UTILITAIRES ---

/**
 * Traduit les statuts de mission de l'API Space Devs
 */
function getStatusLabel($status)
{
    return [
        'Go' => 'Confirmé',
        'TBD' => 'À confirmer',
        'Success' => 'Succès',
        'Failure' => 'Échec',
        'Hold' => 'En pause',
        'Partial Failure' => 'Échec partiel'
    ][$status] ?? $status;
}

/**
 * Formate la date de façon propre pour le site
 */
function formatAstroDate($dateStr)
{
    $date = new DateTime($dateStr);
    return $date->format('d M Y | H:i');
}

function getAstroEvents()
{
    $rssUrl = "https://in-the-sky.org/rss.php?feed=v0";
    error_reporting(0);

    $xml = @simplexml_load_file($rssUrl);
    $events = [];
    $now = time();

    if ($xml && isset($xml->channel->item)) {
        foreach ($xml->channel->item as $item) {
            $eventTimestamp = strtotime((string) $item->pubDate);

            // 1. FILTRAGE : Uniquement le futur
            if ($eventTimestamp >= $now) {
                $events[] = [
                    'title' => (string) $item->title,
                    'desc' => strip_tags((string) $item->description),
                    'date' => date('Y-m-d H:i:s', $eventTimestamp),
                    'timestamp' => $eventTimestamp
                ];
            }
        }
    }

    // 2. TRI : Du plus proche au plus loin
    usort($events, function ($a, $b) {
        return $a['timestamp'] <=> $b['timestamp'];
    });

    // 3. LIMITATION : On ne garde que les 4 premiers
    $events = array_slice($events, 0, 4);

    return json_encode($events, JSON_UNESCAPED_UNICODE);
}

// Traitement pour votre affichage
ob_clean();
$json = getAstroEvents();
$astroEvents = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro | Exploration Spatiale</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        :root {
            --deep-space: #05070a;
            --star-gold: #ffcc00;
            --nebula-blue: #00d4ff;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        :root {
            --neon-blue: #00f2ff;
            --neon-purple: #bc13fe;
            --deep-dark: #050508;
            --glass: rgba(10, 15, 25, 0.7);
        }

        /* --- HEADER ANIMÉ --- */
        .header-container {
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            perspective: 1000px;
        }

        @keyframes titleFloat {

            0%,
            100% {
                transform: translateZ(0px);
                text-shadow: 0 0 20px var(--neon-blue);
            }

            50% {
                transform: translateZ(50px);
                text-shadow: 0 0 40px var(--neon-purple);
            }
        }

        .astro-body {
            background: var(--deep-space);
            color: #fff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* --- ANIMATION ETOILES (Background) --- */
        .stars-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(ellipse at bottom, #1B2735 0%, #090A0F 100%);
        }

        #stars,
        #stars2,
        #stars3 {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent url('https://www.transparenttextures.com/patterns/stardust.png') repeat;
            animation: move-stars 100s linear infinite;
        }

        @keyframes move-stars {
            from {
                background-position: 0 0;
            }

            to {
                background-position: -10000px 5000px;
            }
        }

        /* --- HERO SECTION --- */
        .astro-hero {
            text-align: center;
            padding: 80px 20px;
        }

        .glow-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            letter-spacing: 0;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .glow-text span {
            color: var(--nebula-blue);
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.6);
        }

        .glow-title {
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 0;
            color: #fff;
            text-shadow: 0 0 20px var(--neon-blue), 0 0 40px var(--neon-purple);
            animation: titleFloat 4s ease-in-out infinite;
        }

        /* --- GRID & CARDS --- */
        .astro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .neon-card {
            padding: 25px;
            position: relative;
            transition: 0.3s;
            overflow: hidden;
        }

        /* --- GRID ARTICLES --- */
        .astro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto 100px;
            padding: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-widgets {
                margin-top: 20px;
            }

            .astro-grid {
                grid-template-columns: 1fr;
            }
        }

        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: var(--nebula-blue);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.2);
        }

        .card-anchor {
            text-decoration: none;
            color: inherit;
        }

        .card-header {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .glass-card:hover .card-img {
            transform: scale(1.1);
        }

        .category-pill {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--nebula-blue);
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 2;
            font-family: 'Orbitron', sans-serif;
        }

        .card-content {
            padding: 25px;
        }

        .post-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .post-excerpt {
            font-size: 0.9rem;
            color: #ccd6f6;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .read-more {
            display: block;
            margin-top: 10px;
            color: var(--nebula-blue);
            font-weight: 600;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
            font-size: 0.8rem;
            color: #8892b0;
        }

        .meta i {
            color: var(--nebula-blue);
            margin-right: 5px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .glow-text {
                font-size: 2rem;
            }
        }

        /* --- SECTION COMPTE À REBOURS --- */
        .countdown-section {
            text-align: center;
        }

        .countdown-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .timer-unit {
            background: var(--glass);
            border: 1px solid var(--neon-blue);
            padding: 15px;
            min-width: 80px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 242, 255, 0.2);
        }

        .timer-unit span {
            display: block;
            font-family: 'Orbitron';
            font-size: 2rem;
            color: var(--neon-blue);
        }

        .timer-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* --- TIMELINE TABS --- */
        .event-hub {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .tab-system {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .tab-btn {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: #fff;
            padding: 10px 30px;
            font-family: 'Orbitron';
            cursor: pointer;
            transition: 0.3s;
            clip-path: polygon(10% 0, 100% 0, 90% 100%, 0 100%);
        }

        .tab-btn.active {
            background: var(--neon-purple);
            border-color: var(--neon-purple);
            box-shadow: 0 0 15px var(--neon-purple);
        }

        .event-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .event-item {
            background: var(--glass);
            border-left: 4px solid var(--neon-gold);
            padding: 20px;
            display: none;
            /* Géré par JS */
        }

        .event-item.show {
            display: block;
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* EFFET GLITCH POUR LE TITRE API */
        .glitch-text {
            position: relative;
            display: inline-block;
            color: var(--neon-blue);
            font-family: 'Orbitron';
            font-weight: bold;
            text-transform: uppercase;
            padding: 0 12px;
        }

        .glitch-text::before,
        .glitch-text::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--deep-dark);
            border-radius: 24px;
        }

        .glitch-text::after {
            left: 2px;
            text-shadow: -2px 0 #ff00c1;
            clip: rect(44px, 450px, 56px, 0);
            animation: glitch-anim 5s infinite linear alternate-reverse;
        }

        @keyframes glitch-anim {
            0% {
                clip: rect(31px, 9999px, 94px, 0);
            }

            20% {
                clip: rect(62px, 9999px, 42px, 0);
            }

            /* ... etc ... */
            100% {
                clip: rect(89px, 9999px, 98px, 0);
            }
        }

        .launch-badge {
            background: rgba(0, 242, 255, 0.1);
            border: 1px solid var(--neon-blue);
            padding: 2px 8px;
            font-size: 0.6rem;
            vertical-align: middle;
        }

        /* --- CONFIGURATION DES VARIABLES (THEME UNIFIÉ) --- */
        :root {
            /* Couleurs de base */
            --deep-space: #05070a;
            --deep-dark: #020305;

            /* Accents Néon */
            --neon-blue: #00f2ff;
            --neon-purple: #bc13fe;
            --nebula-blue: #00d4ff;
            --star-gold: #ffcc00;

            /* Glassmorphism */
            --glass: rgba(10, 15, 25, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-hover: rgba(255, 255, 255, 0.15);

            /* Fonts */
            --font-main: 'Inter', sans-serif;
            --font-astro: 'Orbitron', sans-serif;

            --bg-hud: rgba(10, 12, 22, 0.98);
            /* Légèrement plus opaque pour le plein écran */
            --bg-card: rgba(20, 26, 42, 0.4);
            --border-hud: rgba(66, 252, 241, 0.12);
            --color-neon: #66fcf1;
            --color-accent: #ff0055;
            --color-text-main: #f0f4f8;
            --color-text-muted: #8a99ad;
            --font-hud: 'Orbitron', 'Inter', sans-serif;

            --radius-main: 24px;
            --radius-sub: 24px;

            --transition-smooth: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* --- RESET & BASE --- */
        * {
            box-sizing: border-box;
        }

        .astro-body {
            background: var(--deep-space);
            color: #fff;
            font-family: var(--font-main);
            margin: 0;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* --- BACKGROUND ANIMÉ (STARDUST) --- */
        .stars-container {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at center, #1b2735 0%, #05070a 100%);
            overflow: hidden;
        }

        /* Génération de l'effet parallaxe des étoiles */
        #stars,
        #stars2,
        #stars3 {
            position: absolute;
            inset: -1000px;
            /* Débordement pour éviter les coupures lors de l'anim */
            background: transparent url('https://www.transparenttextures.com/patterns/stardust.png') repeat;
        }

        #stars {
            animation: move-stars 150s linear infinite;
            opacity: 0.5;
        }

        #stars2 {
            animation: move-stars 100s linear infinite;
            opacity: 0.3;
            transform: scale(1.5);
        }

        #stars3 {
            animation: move-stars 200s linear infinite;
            filter: hue-rotate(90deg);
        }

        @keyframes move-stars {
            from {
                transform: translate(0, 0);
            }

            to {
                transform: translate(-1000px, 500px);
            }
        }

        /* --- HERO & TITRES --- */
        .astro-hero {
            text-align: center;
            padding: 100px 20px;
            perspective: 1000px;
        }

        .glow-title {
            font-family: var(--font-astro);
            font-size: clamp(2rem, 8vw, 4rem);
            letter-spacing: 0;
            text-transform: uppercase;
            color: #fff;
            text-shadow: 0 0 20px var(--neon-blue), 0 0 40px var(--neon-purple);
            animation: titleFloat 4s ease-in-out infinite;
        }

        @keyframes titleFloat {

            0%,
            100% {
                transform: translateY(0) rotateX(0deg);
                text-shadow: 0 0 20px var(--neon-blue);
            }

            50% {
                transform: translateY(-15px) rotateX(5deg);
                text-shadow: 0 0 40px var(--neon-purple);
            }
        }

        /* --- GRILLE DE CONTENU --- */
        .astro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 35px;
            max-width: 1400px;
            margin: 0 auto 80px;
            padding: 0 25px;
        }

        /* --- CARTES GLASSMORPHISM --- */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
        }

        .glass-card:hover {
            transform: translateY(-12px);
            border-color: var(--neon-blue);
            box-shadow: 0 20px 50px rgba(0, 242, 255, 0.15);
        }

        .card-header {
            position: relative;
            height: 220px;
            border-radius: 24px 24px 0 0;
            overflow: hidden;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .glass-card:hover .card-img {
            transform: scale(1.1);
        }

        .category-pill {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--neon-blue);
            color: var(--deep-space);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            font-family: var(--font-astro);
            text-transform: uppercase;
            box-shadow: 0 0 15px var(--neon-blue);
        }

        .card-content {
            padding: 30px;
        }

        .post-title {
            font-family: var(--font-astro);
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 15px;
        }

        .post-excerpt {
            font-size: 0.95rem;
            color: #b0b8d1;
            margin-bottom: 25px;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid var(--glass-border);
            color: var(--neon-blue);
            font-size: 0.85rem;
        }

        /* --- SYSTÈME DE COMPTE À REBOURS --- */
        .countdown-section {
            padding: 60px 20px;
            background: radial-gradient(circle at center, rgba(188, 19, 254, 0.05) 0%, transparent 70%);
        }

        .countdown-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
            margin-top: 40px;
        }

        .timer-unit {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-bottom: 3px solid var(--neon-blue);
            padding: 20px;
            min-width: 100px;
            border-radius: 16px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .timer-unit span {
            display: block;
            font-family: var(--font-astro);
            font-size: 2.5rem;
            color: var(--neon-blue);
            text-shadow: 0 0 10px rgba(0, 242, 255, 0.5);
        }

        .timer-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #8892b0;
            margin-top: 5px;
        }

        /* --- ONGLETS ÉVÉNEMENTS (EVENT HUB) --- */
        .tab-system {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
        }

        .tab-btn {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            color: #fff;
            padding: 12px 35px;
            font-family: var(--font-astro);
            font-size: 0.8rem;
            cursor: pointer;
            transition: 0.4s;
            clip-path: polygon(15% 0, 100% 0, 85% 100%, 0 100%);
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: var(--neon-purple);
            border-color: var(--neon-purple);
            box-shadow: 0 0 20px rgba(188, 19, 254, 0.4);
            transform: skewX(-5deg);
        }

        /* --- BADGES & GLITCH --- */
        .launch-badge {
            background: rgba(0, 242, 255, 0.1);
            border: 1px solid var(--neon-blue);
            padding: 4px 12px;
            font-size: 0.7rem;
            font-family: var(--font-astro);
            color: var(--neon-blue);
            border-radius: 24px;
        }

        #mission-modal .modal-content {
            border-radius: 24px;
        }

        .glitch-text {
            position: relative;
            font-family: var(--font-astro);
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .astro-grid {
                grid-template-columns: 1fr;
                padding: 15px;
            }

            .glow-title {
                font-size: 2.2rem;
                letter-spacing: 0;
            }

            .timer-unit {
                min-width: 70px;
                padding: 10px;
            }

            .timer-unit span {
                font-size: 1.5rem;
            }

            .tab-btn {
                padding: 10px 15px;
                font-size: 0.7rem;
            }
        }

        /* --- CONTAINER PRINCIPAL --- */
        .terminal-hub {
            max-width: 1400px;
            margin: 0 auto 100px;
            padding: 40px;
            background: rgba(5, 8, 16, 0.8);
            border: 1px solid var(--glass-border);
        }

        /* --- HEADER & STATUS --- */
        .mission-header {
            text-align: center;
            border-bottom: 1px solid rgba(0, 242, 255, 0.1);
            padding-bottom: 40px;
            margin-bottom: 40px;
        }

        .mission-status-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: #00ff88;
            border-radius: 50%;
            box-shadow: 0 0 10px #00ff88;
        }

        .status-text {
            font-size: 0.6rem;
            letter-spacing: 2px;
            color: #00ff88;
            font-family: var(--font-astro);
        }

        .orbitron-sub {
            font-family: var(--font-astro);
            font-size: 0.7rem;
            opacity: 0.6;
            letter-spacing: 4px;
            margin-bottom: 10px;
        }

        .location-tag {
            margin-top: 15px;
            font-size: 0.85rem;
            color: var(--plasma-cyan);
        }

        /* --- HUD COUNTDOWN --- */
        .countdown-hud {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 30px;
        }

        .timer-block {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .timer-block span {
            font-size: 3.5rem;
            font-family: var(--font-astro);
            font-weight: 200;
            color: #fff;
            line-height: 1;
        }

        .timer-block label {
            font-size: 0.6rem;
            letter-spacing: 2px;
            color: var(--plasma-cyan);
            margin-top: 5px;
        }

        .timer-separator {
            font-size: 2rem;
            color: var(--plasma-cyan);
            padding-bottom: 20px;
            opacity: 0.5;
        }

        /* --- COLONNES ÉVÉNEMENTS --- */
        .hub-content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
        }

        .column-title {
            font-family: var(--font-astro);
            font-size: 0.8rem;
            letter-spacing: 2px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid currentColor;
        }

        .blue-theme {
            color: var(--plasma-cyan);
        }

        .gold-theme {
            color: #ffae00;
        }

        /* --- CARTES D'ENTRÉE (ENTRY CARDS) --- */
        .entry-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .entry-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateX(10px);
        }

        .entry-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .entry-date {
            font-size: 0.7rem;
            font-family: var(--font-astro);
            opacity: 0.7;
        }

        .entry-badge-blue {
            font-size: 0.6rem;
            background: rgba(0, 242, 255, 0.1);
            color: var(--plasma-cyan);
            padding: 2px 8px;
            border: 1px solid var(--plasma-cyan);
        }

        .entry-title {
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .entry-action {
            font-size: 0.7rem;
            font-weight: bold;
            color: var(--plasma-cyan);
            text-align: right;
        }

        .entry-preview {
            font-size: 0.75rem;
            opacity: 0.5;
            line-height: 1.4;
        }

        /* --- ANIMATIONS --- */
        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 1;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @media (max-width: 768px) {
            .hub-content-grid {
                grid-template-columns: 1fr;
            }

            .timer-block span {
                font-size: 2rem;
            }

            .terminal-hub {
                padding: 20px;
            }
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            display: none;
            /* Caché par défaut */
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-content {
            max-width: 600px;
            width: 100%;
            position: relative;
            border: 1px solid var(--plasma-cyan);
            box-shadow: 0 0 50px rgba(0, 242, 255, 0.2);
            animation: modalSlide 0.4s cubic-bezier(0.19, 1, 0.22, 1);
        }

        @keyframes modalSlide {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .close-modal {
            background: none;
            border: none;
            color: var(--plasma-cyan);
            font-size: 2rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .close-modal:hover {
            transform: rotate(90deg);
            color: var(--nova-pink);
        }

        .mission-text-scan {
            font-size: 1rem;
            line-height: 1.8;
            color: #e0e0e0;
            border-left: 2px solid var(--plasma-cyan);
            padding-left: 15px;
        }

        .corner-decoration {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, transparent 50%, var(--plasma-cyan) 50%);
            opacity: 0.5;
        }

        /* Base du panneau - Forcé à 100% de la largeur de page */
        .side-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 100vw;
            /* Utilise toute la largeur de l'écran */
            height: 100vh;
            background-color: var(--bg-hud);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: var(--color-text-main);
            z-index: 99999;

            transform: translateX(100%);
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);

            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .side-panel.open {
            transform: translateX(0);
        }

        /* En-tête */
        .panel-header {
            padding: 24px 40px;
            /* Aligné sur la grille large */
            border-bottom: 1px solid var(--border-hud);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(11, 14, 26, 0.5);
        }

        .panel-subtitle {
            font-family: var(--font-hud);
            font-size: 0.7rem;
            color: var(--color-neon);
            letter-spacing: 2px;
            display: block;
            margin-bottom: 4px;
            opacity: 0.8;
        }

        .panel-header h2 {
            margin: 0;
            font-family: var(--font-hud);
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Bouton Fermer Minimaliste */
        .close-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-hud);
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sub);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
            position: relative;
        }

        .close-btn:hover {
            background: rgba(255, 0, 85, 0.1);
            border-color: var(--color-accent);
        }

        .close-btn::before,
        .close-btn::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 2px;
            background-color: var(--color-text-main);
            transition: var(--transition-smooth);
        }

        .close-btn::before {
            transform: rotate(45deg);
        }

        .close-btn::after {
            transform: rotate(-45deg);
        }

        .close-btn:hover::before,
        .close-btn:hover::after {
            background-color: var(--color-accent);
        }

        /* Zone de contenu centré et optimisé pour le grand écran */
        .panel-content {
            padding: 40px;
            flex: 1;
            overflow-y: auto;
            box-sizing: border-box;
        }

        /* Grille adaptative : 1 colonne sur mobile, 2 colonnes équilibrées sur desktop */
        .panel-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            max-width: 1400px;
            /* Empêche le contenu de trop s'éparpiller sur les écrans ultra-larges */
            margin: 0 auto;
            /* Centre le contenu sur la page */
            width: 100%;
        }

        @media (min-width: 992px) {
            .panel-grid {
                grid-template-columns: 450px 1fr;
                /* Largeur fixe pour les médias, le reste pour la data */
                align-items: start;
            }
        }

        /* Conteneur Image */
        .hud-media-container {
            position: relative;
            border-radius: var(--radius-main);
            overflow: hidden;
            background: #000;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 20px;
        }

        .hud-media-container:empty {
            display: none;
        }

        .hud-media-container img {
            display: block;
            width: 100%;
            max-height: 450px;
            /* Augmenté pour le mode plein écran */
            object-fit: cover;
            transition: var(--transition-smooth);
        }

        .hud-media-container::before {
            content: '● LIVE FEED';
            position: absolute;
            top: 16px;
            left: 16px;
            font-family: var(--font-hud);
            font-size: 0.65rem;
            font-weight: bold;
            color: var(--color-accent);
            background: rgba(10, 12, 22, 0.85);
            backdrop-filter: blur(4px);
            padding: 6px 12px;
            border-radius: 20px;
            z-index: 2;
            letter-spacing: 1px;
            border: 1px solid rgba(255, 0, 85, 0.3);
        }

        /* Cartes de données */
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .data-card {
            background: var(--bg-card);
            border: 1px solid var(--border-hud);
            padding: 24px;
            border-radius: var(--radius-main);
            transition: var(--transition-smooth);
        }

        .data-card:hover {
            background: rgba(20, 26, 42, 0.6);
            border-color: rgba(66, 252, 241, 0.3);
        }

        .card-label {
            display: block;
            font-family: var(--font-hud);
            font-size: 0.65rem;
            color: var(--color-neon);
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .card-value {
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--color-text-main);
        }

        /* Section Briefing */
        .mission-briefing {
            border-left: 3px solid var(--color-neon);
            background: rgba(66, 252, 241, 0.02);
        }

        .panel-description {
            line-height: 1.7;
            color: var(--color-text-muted);
            margin: 0;
            font-size: 1rem;
        }

        /* Bouton Live Stream */
        .btn-live {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: var(--color-accent);
            color: #ffffff;
            font-family: var(--font-hud);
            font-size: 0.85rem;
            padding: 18px;
            text-decoration: none;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: var(--radius-main);
            transition: var(--transition-smooth);
            box-shadow: 0 4px 15px rgba(255, 0, 85, 0.3);
        }

        .btn-live:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 0, 85, 0.5);
            filter: brightness(1.1);
        }

        /* L'overlay reste en arrière-plan en cas de transparence */
        .panel-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(4, 5, 10, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .panel-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        body.panel-active {
            overflow: hidden;
        }
    </style>
</head>

<body class="astro-body">

    <div class="stars-container">
        <div id="stars"></div>
        <div id="stars2"></div>
        <div id="stars3"></div>
    </div>

    <?php include "../../__partials/menu.php"; ?>

    <header class="astro-hero header-container" data-aos="zoom-out">
        <h1 class="glow-text glow-title">Exploration <span>Astronomie</span></h1>
        <p>Voyagez à travers les données du cosmos</p>
        <div style="font-family: 'Orbitron'; color: var(--neon-blue); margin-top: 10px;">
            STATION DE DONNÉES <span id="clock">00:00:00</span>
        </div>
    </header>

    <!-- <section class="countdown-section" data-aos="fade-down">
        <p style="color: var(--neon-blue); font-weight: bold;"><?= htmlspecialchars($nextEvent['title']) ?></p>
        <div class="countdown-container" id="main-timer">
            <div class="timer-unit"><span id="days">00</span><div class="timer-label">Jours</div></div>
            <div class="timer-unit"><span id="hours">00</span><div class="timer-label">Heures</div></div>
            <div class="timer-unit"><span id="minutes">00</span><div class="timer-label">Min</div></div>
            <div class="timer-unit"><span id="seconds">00</span><div class="timer-label">Sec</div></div>
        </div>
    </section>

    <section class="event-hub">
        <div class="tab-system">
            <button class="tab-btn active" onclick="filterEvents('future', this)">Événements Futurs</button>
            <button class="tab-btn" onclick="filterEvents('past', this)">Archives Mission</button>
        </div>

        <div class="event-list" id="event-container">
            <?php foreach ($events as $ev): ?>
                <div class="event-item show" data-type="<?= $ev['type'] ?>">
                    <div style="font-size: 0.7rem; color: var(--neon-blue); font-family: 'Orbitron';">
                        <?= date("d/m/Y", strtotime($ev['date'])) ?>
                    </div>
                    <h3 style="margin: 10px 0; font-family: 'Rajdhani';"><?= $ev['title'] ?></h3>
                    <div style="height: 2px; width: 50px; background: var(--neon-purple);"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section> -->

    <main class="container">
        <div class="astro-grid glass-card">
            <div class="neon-card glass-card" data-aos="fade-up" data-aos-delay="100">
                <h3 style="font-family:'Orbitron'; font-size: 0.8rem; color: var(--neon-blue);"><i
                        class="fas fa-satellite-dish"></i> NASA APOD</h3>
                <div id="apod-container"
                    style="height: 150px; margin: 15px 0; border-radius: 8px; overflow: hidden; background: #000; cursor: pointer; transition: transform 0.2s;">
                </div>
                <p id="apod-title" style="font-size: 0.8rem; font-weight: bold;"></p>
            </div>

            <div class="neon-card glass-card" data-aos="fade-up" data-aos-delay="200">
                <h3 style="font-family:'Orbitron'; font-size: 0.8rem; color: var(--neon-purple);"><i
                        class="fas fa-crosshairs"></i> TRACKER ISS</h3>
                <div style="font-size: 1.8rem; margin: 10px 0;">
                    <span id="iss-lat" style="color: #fff">00.00</span>°N<br>
                    <span id="iss-lon" style="color: #fff">00.00</span>°E
                </div>
                <div id="iss-status" style="font-size: 0.7rem; color: var(--neon-blue);">SIGNAL REÇU...</div>
            </div>

            <div class="neon-card glass-card" data-aos="fade-up" data-aos-delay="300">
                <h3 style="font-family:'Orbitron'; font-size: 0.8rem; color: #fff;"><i
                        class="fas fa-user-astronaut"></i> EN ORBITE</h3>
                <div id="humans-count" style="font-size: 4rem; font-weight: 700; line-height: 1;">0</div>
                <p style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Personnes dans le vide</p>
            </div>
        </div>
        <div class="terminal-hub glass-card">
            <header class="mission-header">
                <?php if ($nextLaunch): ?>
                    <?php
                    // 1. Extraction et sécurisation des variables basées sur $nextLaunch
                    $agencyName = $nextLaunch['launch_service_provider']['name'] ?? 'Agence inconnue';
                    $missionDesc = $nextLaunch['mission']['description'] ?? 'Aucune description disponible pour cette mission.';
                    $statusName = $nextLaunch['status']['name'] ?? 'Planifié';
                    $statusId = $nextLaunch['status']['id'] ?? 1;
                    $launchPad = $nextLaunch['pad']['location']['name'] ?? 'Lieu inconnu';
                    $imageUrl = $nextLaunch['image'] ?? null;

                    // 2. Traitement des dates et fuseaux horaires
                    $hasNetDate = !empty($nextLaunch['net']);
                    $isoDate = $hasNetDate ? date('c', strtotime($nextLaunch['net'])) : null;
                    $dateModal = $hasNetDate ? gmdate("d M Y H:i", strtotime($nextLaunch['net'])) : 'Date inconnue';

                    // 3. Extraction propre du lien de streaming direct
                    $liveUrl = null;
                    if (!empty($nextLaunch['vidURLs']) && is_array($nextLaunch['vidURLs'])) {
                        $liveUrl = $nextLaunch['vidURLs'][0]['url'] ?? null;
                    }

                    // 4. Construction de l'objet de données complet destiné au panneau de slide
                    $modalPayload = [
                        'name' => $nextLaunch['name'] ?? 'Mission inconnue',
                        'agency' => $agencyName,
                        'status' => $statusName,
                        'statusId' => $statusId,
                        'desc' => $missionDesc,
                        'pad' => $launchPad,
                        'date' => $dateModal . ' UTC',
                        'isoDate' => $isoDate,
                        'image' => $imageUrl,
                        'live' => $liveUrl
                    ];
                    ?>

                    <div class="mission-status-bar">
                        <span class="status-indicator pulse"></span>
                        <span class="status-text">SYSTÈME OPÉRATIONNEL - LIAISON SATELLITE ÉTABLIE</span>
                    </div>

                    <h2 class="orbitron-sub">PROCHAIN DÉCOLLAGE MONDIAL</h2>

                    <div class="glitch-wrapper">
                        <div class="glitch-text trigger-panel"
                            data-text="<?= htmlspecialchars($nextLaunch['name'] ?? 'Mission inconnue') ?>"
                            data-launch="<?= htmlspecialchars(json_encode($modalPayload), ENT_QUOTES, 'UTF-8') ?>"
                            style="cursor: pointer;">
                            <?= htmlspecialchars($nextLaunch['name'] ?? 'Mission inconnue') ?>
                        </div>
                    </div>

                    <div class="location-tag">
                        <i class="fas fa-satellite"></i>
                        <span><?= htmlspecialchars($nextLaunch['pad']['location']['name'] ?? 'Lieu inconnu') ?></span>
                        <span class="coord-divider">|</span>
                        <span
                            class="launch-date-text"><?= $hasNetDate ? date("d M Y H:i", strtotime($nextLaunch['net'])) : 'Date inconnue' ?>
                            UTC</span>
                    </div>

                    <div class="countdown-hud" id="main-timer">
                        <div class="timer-block">
                            <span id="days">00</span>
                            <label>JOURS</label>
                        </div>
                        <div class="timer-separator">:</div>
                        <div class="timer-block">
                            <span id="hours">00</span>
                            <label>HEURES</label>
                        </div>
                        <div class="timer-separator">:</div>
                        <div class="timer-block">
                            <span id="minutes">00</span>
                            <label>MINUTES</label>
                        </div>
                        <div class="timer-separator">:</div>
                        <div class="timer-block">
                            <span id="seconds">00</span>
                            <label>SECONDES</label>
                        </div>
                    </div>
                <?php endif; ?>
            </header>

            <div class="hub-content-grid">
                <section class="telemetry-column">
                    <h3 class="column-title blue-theme">
                        <i class="fas fa-microchip"></i> MISSIONS EN ATTENTE
                    </h3>
                    <div class="entry-list">
                        <?php foreach ($launches as $index => $launch):
                            // On ignore le premier lancement qui est mis en avant dans le carrousel principal
                            if ($index == 0)
                                continue;

                            // 1. Extraction et sécurisation des variables nécessaires
                            $agencyName = $launch['launch_service_provider']['name'] ?? 'Agence inconnue';
                            $missionDesc = $launch['mission']['description'] ?? 'Aucune description disponible pour cette mission.';
                            $statusName = $launch['status']['name'] ?? 'Planifié';
                            $statusId = $launch['status']['id'] ?? 1;
                            $launchPad = $launch['pad']['location']['name'] ?? 'Lieu inconnu';
                            $imageUrl = $launch['image'] ?? null;

                            // 2. Traitement des dates et fuseaux horaires
                            $hasNetDate = !empty($launch['net']);
                            $isoDate = $hasNetDate ? date('c', strtotime($launch['net'])) : null;
                            $dateMetaUTC = $hasNetDate ? gmdate("d.m.y | H:i", strtotime($launch['net'])) : 'Date inconnue';
                            $dateModal = $hasNetDate ? gmdate("d M Y H:i", strtotime($launch['net'])) : 'Date inconnue';

                            // 3. Extraction propre du lien de streaming direct (si disponible)
                            $liveUrl = null;
                            if (!empty($launch['vidURLs']) && is_array($launch['vidURLs'])) {
                                $liveUrl = $launch['vidURLs'][0]['url'] ?? null;
                            }

                            // 4. Construction de l'objet de données complet destiné à la modal
                            $modalPayload = [
                                'name' => $launch['name'] ?? 'Mission inconnue',
                                'agency' => $agencyName,
                                'status' => $statusName,
                                'statusId' => $statusId,
                                'desc' => $missionDesc,
                                'pad' => $launchPad,
                                'date' => $dateModal . ' UTC',
                                'isoDate' => $isoDate,
                                'image' => $imageUrl,
                                'live' => $liveUrl
                            ];
                            ?>
                            <div class="entry-card mission-entry" data-date="<?= $isoDate ?>"
                                onclick="openMissionModal(<?= htmlspecialchars(json_encode($modalPayload, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>)">

                                <div class="entry-meta">
                                    <span class="entry-date">
                                        <?= htmlspecialchars($dateMetaUTC) ?> UTC
                                    </span>
                                    <span class="entry-badge-blue">
                                        <?= htmlspecialchars($agencyName) ?>
                                    </span>
                                </div>

                                <h4 class="entry-title">
                                    <?= htmlspecialchars($launch['name']) ?>
                                </h4>

                                <div class="entry-footer"
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                    <div class="entry-countdown" data-countdown="<?= $isoDate ?>"
                                        style="font-weight: bold; color: #ff4757; font-size: 0.9em;">00d 00h 00m 00s</div>
                                    <div class="entry-action" style="margin: 0;">
                                        ANALYSE DOSSIER <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php
                // 1. Liste des mots-clés à détecter => l'icône FontAwesome associée
                $iconMapping = [
                    'blue moon' => 'fa-moon',
                    'pleine lune' => 'fa-moon',
                    'nouvelle lune' => 'fa-circle',
                    'éclipse lunaire' => 'fa-cloud-moon',
                    'éclipse solaire' => 'fa-sun',
                    'éclipse' => 'fa-sun',
                    'opposition' => 'fa-arrows-left-right',
                    'conjonction' => 'fa-circle-nodes',
                    'météore' => 'fa-meteor',
                    'étoile filante' => 'fa-meteor',
                    'comète' => 'fa-comet',
                    'aurore' => 'fa-wand-magic-sparkles',
                    'solstice' => 'fa-cloud-sun',
                    'équinoxe' => 'fa-scale-balanced',
                    'transit' => 'fa-shuttle-space',
                ];

                // Icône de secours si aucun mot de la liste n'est trouvé dans le titre
                $defaultIcon = 'fa-star';
                ?>

                <section class="telemetry-column">
                    <h3 class="column-title gold-theme">
                        <i class="fas fa-shuttle-space"></i> PHÉNOMÈNES CÉLESTES
                    </h3>
                    <div class="entry-list">
                        <?php foreach ($astroEvents as $event):
                            // 1. Traitement de l'icône dynamique (votre logique existante)
                            $cleanTitle = mb_strtolower($event['title'] ?? '');
                            $chosenIcon = $defaultIcon;

                            foreach ($iconMapping as $keyword => $icon) {
                                if (str_contains($cleanTitle, $keyword)) {
                                    $chosenIcon = $icon;
                                    break;
                                }
                            }

                            // 2. Extraction et sécurisation des variables nécessaires
                            $eventTitle = $event['title'] ?? 'Événement Astronomique';
                            $eventDesc = $event['desc'] ?? 'Aucune description disponible pour cet événement.';
                            $eventLocation = $event['location'] ?? 'Observation Céleste';
                            $imageUrl = $event['image'] ?? null;
                            $liveUrl = $event['liveUrl'] ?? null;

                            // 3. Traitement des dates (Formatage identique à $launches)
                            $hasDate = !empty($event['date']);
                            $isoDate = $hasDate ? date('c', strtotime($event['date'])) : null;
                            $dateMetaUTC = $hasDate ? date("d.m.y | H:i", strtotime($event['date'])) : 'Date inconnue';
                            $dateModal = $hasDate ? date("d M Y H:i", strtotime($event['date'])) : 'Date inconnue';

                            // 4. Construction de l'objet de données complet destiné à la modal unique
                            $modalPayload = [
                                'name' => $eventTitle,
                                'agency' => 'Événement Astro',
                                'status' => 'Phénomène',
                                'statusId' => 3, // Vert par défaut (#22c55e)
                                'desc' => $eventDesc,
                                'pad' => $eventLocation,
                                'date' => $dateModal . ' UTC',
                                'isoDate' => $isoDate,
                                'image' => $imageUrl,
                                'live' => $liveUrl
                            ];
                            ?>

                            <div class="entry-card mission-entry" data-date="<?= $isoDate ?>"
                                onclick="openMissionModal(<?= htmlspecialchars(json_encode($modalPayload, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>)">

                                <div class="entry-meta">
                                    <span class="entry-date">
                                        <?= htmlspecialchars($dateMetaUTC) ?> UTC
                                    </span>
                                    <span class="entry-badge-blue">
                                        <i class="fas <?= htmlspecialchars($chosenIcon) ?> entry-icon-gold"
                                            style="color: #f59e0b; margin-right: 5px;"></i> astro
                                    </span>
                                </div>

                                <h4 class="entry-title">
                                    <?= htmlspecialchars($eventTitle) ?>
                                </h4>

                                <div class="entry-footer"
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                    <div class="entry-countdown" data-countdown="<?= $isoDate ?>"
                                        style="font-weight: bold; color: #ff4757; font-size: 0.9em;">00d 00h 00m 00s</div>
                                    <div class="entry-action" style="margin: 0;">
                                        ANALYSE DOSSIER <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
        <div>
            <h3
                style="font-family:'Orbitron', sans-serif; font-size: 1rem; margin-bottom: 25px; color: #fff; text-shadow: 0 0 10px rgba(0, 210, 255, 0.5);">
                <i class="fas fa-rss" style="color: #00d2ff; margin-right: 10px;"></i>
                <span style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 5px;">
                    DERNIÈRES <span style="color: #00d2ff;">PUBLICATIONS</span>
                </span>
            </h3>
            <div class="astro-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="glass-card">
                        <a href="contenu-astronomie.php?id=<?= $post['id'] ?>" class="card-anchor">
                            <div class="card-header">
                                <span class="category-pill"><?= htmlspecialchars($post['title']) ?></span>
                                <img src="../../uploads/<?= $post['filename']; ?>" alt="" class="card-img">
                            </div>

                            <div class="card-content">
                                <h2 class="post-title"><?= $post['title_contenu'] ?></h2>

                                <div class="post-excerpt">
                                    <?php
                                    $text = strip_tags($post['contenu']);
                                    echo (strlen($text) > 100) ? substr($text, 0, 100) . '...' : $text;
                                    ?>
                                    <span class="read-more">Lire la suite <i class="fa-solid fa-arrow-right"></i></span>
                                </div>

                                <div class="post-footer">
                                    <div class="meta">
                                        <i class="fa-regular fa-user"></i> <span><?= $post['name'] ?></span>
                                    </div>
                                    <div class="meta">
                                        <i class="fa-regular fa-calendar-days"></i>
                                        <span><?= date("d.m.y", strtotime($post['date_astronomie'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../__partials/footer.php"; ?>

    <div id="apod-modal"
        style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <span id="close-modal"
            style="position: absolute; top: 20px; right: 30px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
        <div id="modal-content"
            style="max-width: 90%; max-height: 90%; display: flex; flex-direction: column; align-items: center;">
            <img id="modal-img"
                style="max-width: 100%; max-height: 80vh; border-radius: 4px; box-shadow: 0 0 20px rgba(255,255,255,0.2);">
            <p id="modal-caption" style="color: white; margin-top: 15px; font-family: sans-serif; text-align: center;">
            </p>
        </div>
    </div>

    <div id="mission-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h2 id="modal-title" class="orbitron-title">DÉTAILS DE LA MISSION</h2>
                <button class="close-modal" onclick="closeMissionModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div id="modal-image-container" class="mb-3 text-center"></div>

                <div id="modal-badge-container" class="d-flex gap-2 mb-3 justify-content-center">
                    <span id="modal-badge-agency" class="badge text-uppercase text-white"
                        style="font-size: 0.75rem; background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4);"></span>
                    <span id="modal-badge-status" class="badge text-white" style="font-size: 0.75rem;"></span>
                </div>

                <p id="modal-description" class="mission-text-scan text-white opacity-75 small mb-3"
                    style="line-height: 1.5; text-align: justify;"></p>

                <div id="modal-countdown-wrapper" class="mx-auto mb-3 text-center" style="display: none;">
                    <div
                        style="font-family: monospace; font-size: 1.1rem; color: #a3e635; background: rgba(0,0,0,0.3); padding: 8px 15px; border-radius: 6px; width: fit-content; border: 1px dashed rgba(163, 230, 53, 0.4); margin: 0 auto;">
                        <i class="fas fa-clock me-1" style="color: #60a5fa;"></i>
                        <span id="modal-countdown-timer">Calcul...</span>
                    </div>
                </div>

                <div id="modal-actions-container" class="d-flex justify-content-center mb-3"></div>

                <div class="modal-footer location-tag small d-flex flex-wrap justify-content-between w-100"
                    style="color: #60a5fa; opacity: 0.95; border-top: 1px solid rgba(59, 130, 246, 0.2); padding-top: 12px; margin-top: 15px;">
                    <div>
                        <i class="fas fa-map-marker-alt" style="color: #a3e635;"></i>
                        <span id="modal-location" class="ms-1"></span>
                    </div>
                    <div>
                        <span id="modal-date" class="text-white"></span>
                    </div>
                </div>
            </div>
            <div class="corner-decoration"></div>
        </div>
    </div>

    <div id="side-panel" class="side-panel">
        <div class="panel-header">
            <div class="header-title-wrapper">
                <span class="panel-subtitle">// MISSION TELEMETRY VIEW</span>
                <h2 id="panel-title">Nom de la Mission</h2>
            </div>
            <button id="close-panel" class="close-btn" aria-label="Fermer la vue">
                <span class="close-icon"></span>
            </button>
        </div>

        <div class="panel-content">
            <div class="panel-grid">

                <div class="panel-col-left">
                    <div id="panel-image-wrapper" class="hud-media-container"></div>
                    <div id="panel-live-wrapper"></div>
                </div>

                <div class="panel-col-right">
                    <div class="metadata-grid">
                        <div class="data-card">
                            <span class="card-label">AGENCE SPATIALE</span>
                            <div class="card-value" id="panel-agency">-</div>
                        </div>

                        <div class="data-card">
                            <span class="card-label">STATUT DE MISSION</span>
                            <div class="card-value" id="panel-status">-</div>
                        </div>

                        <div class="data-card">
                            <span class="card-label">FENÊTRE DE LANCEMENT</span>
                            <div class="card-value" id="panel-date">-</div>
                        </div>

                        <div class="data-card">
                            <span class="card-label">ZONE DE SÉCURITÉ / PAD</span>
                            <div class="card-value" id="panel-pad">-</div>
                        </div>
                    </div>

                    <div class="data-card mission-briefing">
                        <span class="card-label">MISSION BRIEFING</span>
                        <p id="panel-desc" class="panel-description">-</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="panel-overlay" class="panel-overlay"></div>

    <script src="/js/divers.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- 1. INITIALISATION ---
            AOS.init({ duration: 1000, once: true });

            // --- 2. HORLOGE TEMPS RÉEL ---
            const updateClock = () => {
                document.getElementById('clock').innerText = new Date().toLocaleTimeString();
            };
            setInterval(updateClock, 1000);
            updateClock();

            // --- 3. NASA APOD (AVEC POPUP) ---
            const apodContainer = document.getElementById('apod-container');
            const modal = document.getElementById('apod-modal'); // Assurez-vous d'avoir l'ID dans votre HTML
            const modalImg = document.getElementById('modal-img');

            // Vérification si la donnée du jour existe déjà en local
            if (localStorage.getItem('apod_date') === new Date().toDateString() && localStorage.getItem('apod_data')) {

                // Chargement instantané depuis le stockage local (0 requête API)
                const data = JSON.parse(localStorage.getItem('apod_data'));

                document.getElementById('apod-title').innerText = data.title;

                if (data.media_type === 'image') {
                    apodContainer.innerHTML = `<img src="${data.url}" alt="${data.title}" style="width:100%; height:100%; object-fit:cover; cursor:pointer;">`;

                    apodContainer.onclick = () => {
                        modalImg.src = data.hdurl || data.url;
                        modal.style.display = "flex";
                        setTimeout(() => modal.style.opacity = "1", 10);
                    };
                } else {
                    apodContainer.innerHTML = `<iframe src="${data.url}" style="width:100%; height:100%; border:none;"></iframe>`;
                }

            } else {

                // Si pas en cache, appel à l'API NASA
                fetch('https://api.nasa.gov/planetary/apod?api_key=DEMO_KEY')
                    .then(r => r.json())
                    .then(data => {
                        // Sauvegarde dans le localStorage
                        localStorage.setItem('apod_data', JSON.stringify(data));
                        localStorage.setItem('apod_date', new Date().toDateString());

                        document.getElementById('apod-title').innerText = data.title;

                        if (data.media_type === 'image') {
                            apodContainer.innerHTML = `<img src="${data.url}" alt="${data.title}" style="width:100%; height:100%; object-fit:cover; cursor:pointer;">`;

                            // Logique d'agrandissement au clic
                            apodContainer.onclick = () => {
                                modalImg.src = data.hdurl || data.url;
                                modal.style.display = "flex";
                                setTimeout(() => modal.style.opacity = "1", 10);
                            };
                        } else {
                            apodContainer.innerHTML = `<iframe src="${data.url}" style="width:100%; height:100%; border:none;"></iframe>`;
                        }
                    })
                    .catch(err => console.error("Erreur APOD:", err));
            }

            // Fermeture du modal (clic n'importe où sur le modal)
            if (modal) {
                modal.onclick = () => {
                    modal.style.opacity = "0";
                    setTimeout(() => modal.style.display = "none", 300);
                };
            }

            // --- 4. POSITION DE L'ISS ---
            async function fetchISS() {
                try {
                    const response = await fetch('https://api.wheretheiss.at/v1/satellites/25544');
                    const data = await response.json();
                    document.getElementById('iss-lat').innerText = data.latitude.toFixed(2);
                    document.getElementById('iss-lon').innerText = data.longitude.toFixed(2);
                } catch (err) {
                    console.error("Erreur ISS:", err);
                }
            }
            setInterval(fetchISS, 5000);
            fetchISS();

            // --- 5. HUMAINS DANS L'ESPACE ---
            fetch('https://corquaid.github.io/international-space-station-APIs/JSON/people-in-space.json')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('humans-count').innerText = data.number;
                })
                .catch(() => { document.getElementById('humans-count').innerText = "10+"; });

            // --- 6. COMPTE À REBOURS (Éclipse du 12 août 2026) ---
            const targetDate = new Date("August 12, 2026 18:00:00").getTime();

            const updateTimer = () => {
                const diff = targetDate - new Date().getTime();
                if (diff <= 0) return;

                const d = Math.floor(diff / 86400000);
                const h = Math.floor((diff % 86400000) / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);

                const ids = ['days', 'hours', 'minutes', 'seconds'];
                const values = [d, h, m, s];

                ids.forEach((id, i) => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = values[i].toString().padStart(2, '0');
                });
            };
            setInterval(updateTimer, 1000);
            updateTimer();

            // --- 7. FILTRAGE DES ÉVÉNEMENTS ---
            window.filterEvents = function (type, btn) {
                // Update Boutons
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Update Items avec animation
                document.querySelectorAll('.event-item').forEach(item => {
                    if (item.dataset.type === type) {
                        item.style.display = "block";
                        setTimeout(() => item.classList.add('show'), 10);
                    } else {
                        item.classList.remove('show');
                        setTimeout(() => item.style.display = "none", 300);
                    }
                });
            };

            // Initialisation du premier onglet
            const firstTab = document.querySelector('.tab-btn');
            if (firstTab) filterEvents('future', firstTab);
        });

        // LOGIQUE DU COMPTE À REBOURS
        // const targetDate = new Date("August 12, 2026 18:00:00").getTime();

        // function updateTimer() {
        //     const now = new Date().getTime();
        //     const diff = targetDate - now;

        //     const d = Math.floor(diff / (1000 * 60 * 60 * 24));
        //     const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        //     const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        //     const s = Math.floor((diff % (1000 * 60)) / 1000);

        //     document.getElementById('days').innerText = d.toString().padStart(2, '0');
        //     document.getElementById('hours').innerText = h.toString().padStart(2, '0');
        //     document.getElementById('minutes').innerText = m.toString().padStart(2, '0');
        //     document.getElementById('seconds').innerText = s.toString().padStart(2, '0');
        // }
        // setInterval(updateTimer, 1000);

        // // FILTRAGE DES ÉVÉNEMENTS
        // function filterEvents(type, btn) {
        //     // Update Boutons
        //     document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        //     btn.classList.add('active');

        //     // Update Items
        //     document.querySelectorAll('.event-item').forEach(item => {
        //         if(item.dataset.type === type) {
        //             item.classList.add('show');
        //         } else {
        //             item.classList.remove('show');
        //         }
        //     });
        // }
        // // Initialiser sur 'future' par défaut
        // filterEvents('future', document.querySelector('.tab-btn'));
    </script>
    <script>
        /**
 * METEASTRO - UNITÉ DE CONTRÔLE MISSION (V2.0)
 * Système de télémétrie temporelle et Traduction Cognitive
 */
        document.addEventListener('DOMContentLoaded', () => {

            // --- 1. MOTEUR DE COMPTE À REBOURS (UTC SAFE) ---
            const launchDateRaw = "<?= $nextLaunch['net'] ?? '' ?>";

            if (launchDateRaw) {
                const targetDate = new Date(launchDateRaw).getTime();
                const timerContainer = document.getElementById('main-timer');
                const units = {
                    days: document.getElementById('days'),
                    hours: document.getElementById('hours'),
                    minutes: document.getElementById('minutes'),
                    seconds: document.getElementById('seconds')
                };

                const updateClock = () => {
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    if (distance <= 0) {
                        if (timerContainer) {
                            timerContainer.innerHTML = `
                        <div class="liftoff-alert" style="color: var(--neon-blue); font-family: 'Orbitron'; font-size: 1.2rem; animation: pulse 1s infinite;">
                            <i class="fas fa-rocket"></i> NOMINAL : DÉCOLLAGE EN COURS
                        </div>`;
                        }
                        return;
                    }

                    // Calculs temporels
                    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);

                    // Mise à jour du DOM avec padding
                    if (units.days) units.days.innerText = d.toString().padStart(2, '0');
                    if (units.hours) units.hours.innerText = h.toString().padStart(2, '0');
                    if (units.minutes) units.minutes.innerText = m.toString().padStart(2, '0');
                    if (units.seconds) units.seconds.innerText = s.toString().padStart(2, '0');
                };

                updateClock();
                setInterval(updateClock, 1000);
            }

            // --- 2. MODULE DE TRADUCTION INTELLIGENTE (LAZY-LOADING) ---

            const translateText = async (text, element) => {
                if (!text || text.length < 5 || element.dataset.translated === "true") return;

                // Nettoyage du texte source
                const cleanText = text.replace(/\s+/g, " ").trim();

                try {
                    element.style.opacity = "0.5";
                    element.innerHTML = '<i class="fas fa-sync fa-spin"></i> Traduction en cours...';

                    const response = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(cleanText)}&langpair=en|fr`);
                    const data = await response.json();

                    if (data.responseData?.translatedText) {
                        element.style.transition = "all 0.6s ease";
                        element.style.opacity = "0";

                        setTimeout(() => {
                            element.innerText = data.responseData.translatedText;
                            element.style.opacity = "1";
                            element.dataset.translated = "true";
                            element.style.color = "var(--text-light)";
                        }, 400);
                    }
                } catch (err) {
                    console.error("Échec liaison satellite (traduction) :", err);
                    element.innerText = cleanText;
                    element.style.opacity = "1";
                }
            };

            // --- 3. OBSERVATEUR DE DÉFILEMENT (INTERSECTION OBSERVER) ---
            // On ne traduit que si l'élément est visible à l'écran !
            const observerOptions = { threshold: 0.2 };

            const translationObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const rawText = el.getAttribute('data-raw-text');
                        translateText(rawText, el);
                        observer.unobserve(el); // On arrête d'observer une fois traduit
                    }
                });
            }, observerOptions);

            // Initialisation des cibles de traduction
            document.querySelectorAll('.mission-desc-text').forEach(el => {
                translationObserver.observe(el);
            });
        });

        // Variable globale pour stocker l'intervalle du compte à rebours
        let modalCountdownInterval = null;

        function openMissionModal(data) {
            const modal = document.getElementById('mission-modal');
            if (!modal || !data) return;

            // 1. Assignation des textes simples
            document.getElementById('modal-title').innerText = data.name || 'DÉTAILS DE LA MISSION';
            document.getElementById('modal-description').innerText = data.desc || 'Aucune description disponible.';
            document.getElementById('modal-location').innerText = data.pad || 'Lieu inconnu';
            document.getElementById('modal-date').innerHTML = `<i class="fa-regular fa-calendar"></i> ${data.date || 'Date inconnue'}`;

            // 2. Gestion des Badges
            if (document.getElementById('modal-badge-agency')) {
                document.getElementById('modal-badge-agency').innerHTML = `<i class="fas fa-space-shuttle me-1"></i> ${data.agency || 'Inconnue'}`;
            }
            const badgeStatus = document.getElementById('modal-badge-status');
            if (badgeStatus) {
                badgeStatus.innerText = data.status || 'Planifié';
                badgeStatus.style.backgroundColor = (data.statusId === 3) ? '#22c55e' : '#3b82f6';
            }

            // 3. Gestion de l'Image
            const imageContainer = document.getElementById('modal-image-container');
            if (imageContainer) {
                if (data.image) {
                    imageContainer.innerHTML = `<img src="${data.image}" alt="${data.name}" style="width: 100%; max-width: 100%; height: 220px; object-fit: cover; border-radius: 1rem; border: 1px solid rgba(59, 130, 246, 0.3);">`;
                } else {
                    imageContainer.innerHTML = `<div class="glass-card d-flex align-items-center justify-content-center mx-auto" style="width: 100%; height: 220px; border-radius: 1rem; border: 1px solid rgba(59, 130, 246, 0.2); background: rgba(59, 130, 246, 0.05);"><i class="fas fa-rocket fa-3x" style="color: rgba(96, 165, 250, 0.4);"></i></div>`;
                }
            }

            // 4. NOUVEAU : Gestion dynamique du Compte à Rebours
            const countdownWrapper = document.getElementById('modal-countdown-wrapper');
            const countdownTimer = document.getElementById('modal-countdown-timer');

            // On nettoie l'ancien intervalle s'il y en avait un
            clearInterval(modalCountdownInterval);

            if (data.isoDate) {
                const targetDate = new Date(data.isoDate).getTime();

                if (!isNaN(targetDate)) {
                    countdownWrapper.style.display = 'block';

                    // Fonction de mise à jour du temps
                    const updateTimer = () => {
                        const now = new Date().getTime();
                        const difference = targetDate - now;

                        if (difference <= 0) {
                            countdownTimer.innerText = "LANCEMENT EN COURS / TERMINÉ";
                            clearInterval(modalCountdownInterval);
                            return;
                        }

                        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        countdownTimer.innerText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                    };

                    updateTimer(); // Exécution immédiate
                    modalCountdownInterval = setInterval(updateTimer, 1000); // Boucle toutes les secondes
                } else {
                    countdownWrapper.style.display = 'none';
                }
            } else {
                countdownWrapper.style.display = 'none';
            }

            // 5. Gestion du bouton Live
            const actionsContainer = document.getElementById('modal-actions-container');
            if (actionsContainer) {
                actionsContainer.innerHTML = '';
                if (data.live) {
                    actionsContainer.innerHTML = `
                <a href="${data.live}" target="_blank" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-2 pulse-live-btn" style="border-radius: 6px; padding: 9px 15px; font-weight: bold; background: #ef4444; border: none; text-decoration: none; color: #fff;">
                    <i class="fas fa-video"></i> DIRECT
                </a>`;
                }
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeMissionModal() {
            const modal = document.getElementById('mission-modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';

                // Arrêt du compte à rebours à la fermeture
                clearInterval(modalCountdownInterval);
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function updateCountdowns() {
                const now = new Date().getTime();
                const cards = document.querySelectorAll('.mission-entry');

                cards.forEach(card => {
                    const targetDateStr = card.getAttribute('data-date');
                    const countdownElement = card.querySelector('.entry-countdown');

                    if (!targetDateStr || !countdownElement) return;

                    const targetDate = new Date(targetDateStr).getTime();
                    const distance = targetDate - now;

                    if (distance < 0) {
                        countdownElement.innerHTML = "Lancé !";
                        countdownElement.style.color = "#2ed573";
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    let countdownStr = "";
                    if (days > 0) countdownStr += `${days}j `;
                    countdownStr += `${hours.toString().padStart(2, '0')}h `;
                    countdownStr += `${minutes.toString().padStart(2, '0')}m `;
                    countdownStr += `${seconds.toString().padStart(2, '0')}s`;

                    countdownElement.innerHTML = `${countdownStr}`;
                });
            }

            updateCountdowns();
            setInterval(updateCountdowns, 1000);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const trigger = document.querySelector('.trigger-panel');
            const panel = document.getElementById('side-panel');
            const closeBtn = document.getElementById('close-panel');
            const overlay = document.getElementById('panel-overlay');

            const pTitle = document.getElementById('panel-title');
            const pAgency = document.getElementById('panel-agency');
            const pStatus = document.getElementById('panel-status');
            const pDate = document.getElementById('panel-date');
            const pPad = document.getElementById('panel-pad');
            const pDesc = document.getElementById('panel-desc');
            const pImageWrapper = document.getElementById('panel-image-wrapper');
            const pLiveWrapper = document.getElementById('panel-live-wrapper');

            if (trigger) {
                trigger.addEventListener('click', () => {
                    const rawData = trigger.getAttribute('data-launch');

                    if (!rawData) {
                        console.error("Erreur : L'attribut data-launch est vide.");
                        return;
                    }

                    try {
                        const data = JSON.parse(rawData);
                        if (!data) return;

                        // Injection sécurisée des textes
                        pTitle.textContent = data.name || 'Mission inconnue';
                        pAgency.textContent = data.agency || 'Agence inconnue';
                        pStatus.textContent = data.status || 'Statut inconnu';
                        pDate.textContent = data.date || 'Date inconnue';
                        pPad.textContent = data.pad || 'Lieu inconnu';
                        pDesc.textContent = data.desc || 'Aucune description disponible.';

                        // Gestion de l'image (sans styles codés en dur)
                        if (data.image) {
                            pImageWrapper.innerHTML = `<img src="${data.image}" alt="${data.name}">`;
                        } else {
                            pImageWrapper.innerHTML = '';
                        }

                        // Gestion du bouton Live
                        if (data.live) {
                            pLiveWrapper.innerHTML = `
                        <a href="${data.live}" target="_blank" class="btn-live">
                            REGARDER LE LIVE STREAM
                        </a>
                    `;
                        } else {
                            pLiveWrapper.innerHTML = '';
                        }

                        // Ouverture fluide
                        panel.classList.add('open');
                        if (overlay) overlay.classList.add('open');
                        document.body.classList.add('panel-active');

                    } catch (error) {
                        console.error("Erreur parsing data-launch :", error);
                    }
                });
            }

            const closePanel = () => {
                panel.classList.remove('open');
                if (overlay) overlay.classList.remove('open');
                document.body.classList.remove('panel-active');
            };

            if (closeBtn) closeBtn.addEventListener('click', closePanel);
            if (overlay) overlay.addEventListener('click', closePanel);
        });
    </script>
</body>

</html>