<?php
declare(strict_types=1);

/**
 * check-and-send-newsletter.php
 * Destiné à être exécuté par un cron job (aucun contexte HTTP/navigateur).
 * Vérifie s'il y a des abonnés en attente de la dernière version, envoie la newsletter si besoin.
 *
 * Protections anti-surcharge :
 * - Verrou fichier : empêche deux exécutions simultanées si le cron se chevauche.
 * - Envoi par lot (BATCH_SIZE) : ne traite qu'un nombre limité d'abonnés par exécution,
 *   pour rester sous les seuils anti-spam du fournisseur SMTP.
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$logFile = __DIR__ . '/../logs/newsletter-cron.log';
$lockFile = __DIR__ . '/../logs/newsletter-cron.lock';

const BATCH_SIZE = 30; // Nombre max d'emails envoyés par exécution du cron

function logCron(string $message, string $logFile): void
{
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            error_log("Impossible de créer le dossier de logs : $logDir");
            return;
        }
    }

    // Horodatage explicitement en fuseau Paris, indépendamment du fuseau
    // par défaut du serveur (souvent UTC sur un hébergement mutualisé).
    $timestamp = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $line = '[' . $timestamp->format('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

    if (file_put_contents($logFile, $line, FILE_APPEND) === false) {
        error_log("Échec d'écriture dans le fichier de log : $logFile");
    }
}

// --- Verrou anti-chevauchement ---
// S'assure qu'une seule exécution de ce script tourne à la fois, même si
// le cron se redéclenche avant la fin d'une exécution précédente (lenteur
// réseau SMTP, grosse volumétrie, etc.).
$lockDir = dirname($lockFile);
if (!is_dir($lockDir)) {
    mkdir($lockDir, 0755, true);
}

$lockHandle = fopen($lockFile, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    logCron('Exécution ignorée : une instance est déjà en cours.', $logFile);
    echo json_encode([
        'status' => 'skipped',
        'message' => 'Une exécution est déjà en cours, celle-ci a été ignorée.',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    // --- 1. Dépendances ---
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    $dbPath = __DIR__ . '/../__partials/db.class.php';
    $newsletterScript = __DIR__ . '/send-newsletter-release.php';
    $logoPath = __DIR__ . '/../ressources/logo.png';

    foreach ([
        'Autoload' => $autoloadPath,
        'Base de données' => $dbPath,
        'Script newsletter' => $newsletterScript,
    ] as $label => $path) {
        if (!file_exists($path)) {
            throw new RuntimeException("Fichier introuvable ($label) : $path");
        }
    }

    require_once $autoloadPath;
    require_once $dbPath;
    require_once $newsletterScript;

    // --- 2. Configuration ---
    $latestVersion = '2.5.3';
    $releaseDate = '20/08/2026';
    $minRequiredVersion = '1.0.0';

    $changelog = [
        'Correction de l\'envoie du code de vérification par mail',
    ];

    // --- 3. Connexion BDD ---
    $obj = new Db();

    // --- 4. Comptage des abonnés en attente ---
    $pending = $obj->query2(
        "SELECT COUNT(*) AS total FROM `users`
         WHERE `newsletter` = 1 AND (`last_version` IS NULL OR `last_version` != ?)",
        [$latestVersion]
    );
    $usersToNotifyCount = (int) ($pending[0]['total'] ?? 0);

    logCron("Vérification version $latestVersion : $usersToNotifyCount abonné(s) en attente (lot max : " . BATCH_SIZE . ").", $logFile);

    // --- 5. Envoi si nécessaire (limité à BATCH_SIZE par exécution) ---
    $newsletterExecuted = false;
    $newsletterResult = [
        'sent_count' => 0,
        'total' => $usersToNotifyCount,
        'errors' => [],
    ];

    if ($usersToNotifyCount > 0) {
        // sendNewsletterRelease doit accepter ce 4e paramètre pour limiter
        // le nombre d'abonnés traités en une seule exécution (voir fonction associée).
        $newsletterResult = sendNewsletterRelease($obj, $latestVersion, $logoPath, BATCH_SIZE);
        $newsletterExecuted = true;

        $remaining = max(0, $usersToNotifyCount - $newsletterResult['sent_count']);

        logCron(sprintf(
            "Envoi terminé : %d/%d réussi(s) sur ce lot, %d erreur(s), %d abonné(s) restant(s) pour la prochaine exécution.",
            $newsletterResult['sent_count'],
            $newsletterResult['total'],
            count($newsletterResult['errors']),
            $remaining
        ), $logFile);

        foreach ($newsletterResult['errors'] as $error) {
            logCron("Erreur envoi : $error", $logFile);
        }
    }

    // --- 6. Réponse ---
    echo json_encode([
        'status' => 'success',
        'version' => $latestVersion,
        'release_date' => $releaseDate,
        'min_required_version' => $minRequiredVersion,
        'newsletter_sent' => $newsletterExecuted,
        'message' => $newsletterExecuted
            ? "Newsletter envoyée à {$newsletterResult['sent_count']} utilisateur(s) sur ce lot."
            : "Tous les utilisateurs ont déjà reçu la notification pour la version {$latestVersion}.",
        'newsletter_stats' => [
            'emails_sent' => $newsletterResult['sent_count'],
            'total_pending_users' => $usersToNotifyCount,
            'batch_size' => BATCH_SIZE,
            'errors' => $newsletterResult['errors'],
        ],
        'changelog' => $changelog,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    logCron('ERREUR FATALE : ' . $e->getMessage(), $logFile);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} finally {
    // Libère systématiquement le verrou, même en cas d'erreur,
    // pour ne jamais bloquer les exécutions suivantes.
    if (is_resource($lockHandle)) {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }
}

exit();