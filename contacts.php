<?php
/**
 * METEASTRO - Système de transmission de signaux
 * Version 2.0 : Validation DNS + Sécurité PHP 8.2+
 */

// 1. Environnement et Entêtes
ob_start();
header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Chemin sécurisé vers l'autoloader
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo json_encode(['status' => 'error', 'message' => 'Système incomplet (Autoload manquant).']);
    exit;
}
require $autoload;

/**
 * Réponse JSON propre
 */
function sendResponse(string $status, string $message): void
{
    ob_clean();
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'timestamp' => time()
    ]);
    exit;
}

// 2. Récupération et Nettoyage des données
$pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// 3. Validations de sécurité
if (!$pseudo || !$email || !$message) {
    sendResponse('error', 'Données invalides ou email mal formé.');
}

// --- VÉRIFICATION SI L'EMAIL EXISTE (Domaine et MX) ---
$domain = substr(strrchr($email, "@"), 1);
if (!checkdnsrr($domain, "MX")) {
    sendResponse('error', "La destination @$domain est introuvable dans la galaxie (Email inexistant).");
}

// 4. Traitement PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuration Serveur
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dvmta39@gmail.com';
    $mail->Password = 'pnnikshkztituxfj'; // Mot de passe d'application uniquement
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;

    // Destinataires
    $mail->setFrom('dvmta39@gmail.com', 'Meteastro - Station de Contrôle');
    $mail->addAddress('dvmta39@gmail.com');
    $mail->addReplyTo($email, $pseudo);

    // Intégration Logo
    $logoPath = __DIR__ . '/ressources/logo.png';
    if (file_exists($logoPath)) {
        $mail->addEmbeddedImage($logoPath, 'meteastro_logo');
        $logoSrc = 'cid:meteastro_logo';
    } else {
        $logoSrc = 'https://votre-site.com/ressources/logo.png';
    }

    // Contenu HTML
    $mail->isHTML(true);
    $mail->Subject = "🪐 [METEASTRO] Transmission de $pseudo";

    $mail->Body = "
    <div style='background-color: #020617; padding: 40px 10px; font-family: Arial, sans-serif;'>
        <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #0f172a; border-radius: 12px; border: 1px solid #1e293b; color: #f1f5f9;'>
            <tr>
                <td align='center' style='padding: 30px; background: #1e293b; border-radius: 12px 12px 0 0;'>
                    <img src='{$logoSrc}' alt='Meteastro' width='70' style='margin-bottom: 10px;'>
                    <div style='color: #38bdf8; font-size: 10px; text-transform: uppercase; letter-spacing: 3px;'>Station de Communication</div>
                </td>
            </tr>
            <tr>
                <td style='padding: 30px;'>
                    <h2 style='font-size: 18px; color: #38bdf8;'>Signal capté de : {$pseudo}</h2>
                    <div style='background: #020617; border: 1px solid #334155; padding: 20px; border-radius: 8px; line-height: 1.6; color: #e2e8f0;'>
                        " . nl2br($message) . "
                    </div>
                    <p style='margin-top: 25px; font-size: 13px; color: #94a3b8;'>
                        <strong>Source :</strong> {$email}
                    </p>
                </td>
            </tr>
            <tr>
                <td align='center' style='padding: 20px; font-size: 10px; color: #475569; border-top: 1px solid #1e293b;'>
                    METEASTRO SYSTEM &copy; 2026
                </td>
            </tr>
        </table>
    </div>";

    $mail->AltBody = "Message de $pseudo ($email) : \n\n $message";

    $mail->send();

    sendResponse('success', 'Signal propulsé avec succès vers Meteastro !');

} catch (Exception $e) {
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
    sendResponse('error', 'Le signal a été dévié par une anomalie (Erreur d\'envoi).');
}

ob_end_flush();