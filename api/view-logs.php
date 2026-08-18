<?php
declare(strict_types=1);
session_start();

header('Content-Type: application/json; charset=utf-8');

// Le token est transmis via l'en-tête X-CSRF-Token par le JS de la page principale,
// et non plus en POST/GET (évite qu'il traîne dans les logs serveur ou l'URL).
$tokenRecu = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

if (!$tokenRecu || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecu)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé : token invalide ou expiré.']);
    exit();
}

$logFile = __DIR__ . '/../logs/newsletter-cron.log';
$maxLines = 200; // Évite de charger un fichier de log devenu énorme

if (!file_exists($logFile)) {
    echo json_encode([
        'status' => 'success',
        'exists' => false,
        'message' => 'Aucun log trouvé pour le moment.',
        'lines' => [],
    ]);
    exit();
}

// Lecture des dernières lignes seulement (le fichier peut grossir avec le temps)
$allLines = file($logFile, FILE_IGNORE_NEW_LINES);

if ($allLines === false) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Impossible de lire le fichier de log (permissions ?).',
    ]);
    exit();
}

$lastLines = array_slice($allLines, -$maxLines);

echo json_encode([
    'status' => 'success',
    'exists' => true,
    'total_lines' => count($allLines),
    'lines' => $lastLines,
], JSON_UNESCAPED_UNICODE);

exit();