<?php
// api/send-announcement-v250.php

set_time_limit(0);
ini_set('display_errors', 0);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    $dbPath = __DIR__ . '/../__partials/db.class.php';

    if (!file_exists($autoloadPath) || !file_exists($dbPath)) {
        throw new Exception("Fichiers système introuvables.");
    }

    require_once $autoloadPath;
    require_once $dbPath;

    $obj = new Db();

    // 1. Récupération de l'ensemble des utilisateurs enregistrés
    $sql = "SELECT `email`, `name` FROM `users` WHERE `email` IS NOT NULL AND `email` != '' AND `status` = 'verified'";
    $users = $obj->query($sql);

    if (empty($users)) {
        exit("Aucun utilisateur trouvé en base de données.\n");
    }

    $logoPath = __DIR__ . '/../ressources/logo.png';
    $sentCount = 0;

    // 2. Boucle d'envoi du mail d'information
    foreach ($users as $user) {
        $userEmail = filter_var($user['email'], FILTER_VALIDATE_EMAIL);
        $userName = !empty($user['name']) ? trim($user['name']) : 'Astronome';

        if (!$userEmail) {
            continue;
        }

        try {
            $mail = new PHPMailer(true);

            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com';
            $mail->Password = 'pnnikshkztituxfj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Expéditeur et destinataire
            $mail->setFrom('dvmta39@gmail.com', 'Meteastro - Communication');
            $mail->addAddress($userEmail, $userName);
            $mail->addReplyTo('dvmta39@gmail.com', 'Support Meteastro');

            // Intégration du logo
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'meteastro_logo');
                $logoSrc = 'cid:meteastro_logo';
            } else {
                $logoSrc = 'https://meteastro.com/ressources/logo.png';
            }

            $pseudo = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
            $emailEscaped = htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8');

            $mail->isHTML(true);
            $mail->Subject = "📩 [METEASTRO] Arrivée de la Newsletter sur la version 2.5.0";

            $mail->Body = "
            <div style='background-color: #020617; padding: 40px 10px; font-family: Arial, sans-serif;'>
                <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #0f172a; border-radius: 12px; border: 1px solid #1e293b; color: #f1f5f9;'>
                    <tr>
                        <td align='center' style='padding: 30px; background: #1e293b; border-radius: 12px 12px 0 0;'>
                            <img src='{$logoSrc}' alt='Meteastro' width='80' style='margin-bottom: 12px;'>
                            <div style='color: #38bdf8; font-size: 11px; text-transform: uppercase; letter-spacing: 3px; font-weight: bold;'>Station de Communication Meteastro</div>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 30px;'>
                            <h2 style='font-size: 20px; color: #38bdf8; margin-top: 0;'>Bonjour {$pseudo},</h2>
                            
                            <p style='color: #e2e8f0; line-height: 1.6; font-size: 14px;'>
                                La version <strong>2.5.0</strong> de Meteastro intègre désormais une <strong>Newsletter officielle</strong>.
                            </p>

                            <div style='background: #020617; border: 1px solid #334155; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                                <h3 style='font-size: 15px; color: #f59e0b; margin-top: 0; text-transform: uppercase;'>
                                    📬 À quoi sert la Newsletter ?
                                </h3>
                                <p style='color: #cbd5e1; font-size: 13px; line-height: 1.6; margin: 0;'>
                                    Elle vous permettra de recevoir directement par e-mail les bulletins d'observation, les alertes d'événements astronomiques majeurs et les actualités de la plateforme.
                                </p>
                            </div>

                            <p style='color: #e2e8f0; line-height: 1.6; font-size: 14px;'>
                                Vous pouvez gérer vos préférences d'abonnement à tout moment depuis les paramètres de votre compte.
                            </p>

                            <div align='center' style='margin: 30px 0 15px 0;'>
                                <a href='https://meteastro.fr' style='background-color: #0284c7; color: #ffffff; padding: 12px 28px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 25px; display: inline-block;'>
                                    Accéder à Meteastro
                                </a>
                            </div>

                            <p style='margin-top: 25px; font-size: 12px; color: #64748b; text-align: center;'>
                                Transmission envoyée à l'adresse <strong>{$emailEscaped}</strong>.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>";

            $mail->send();
            $sentCount++;

        } catch (Exception $e) {
            error_log("Erreur d'envoi pour {$userEmail} : " . $e->getMessage());
        }
    }

    echo "Annonce envoyée avec succès à {$sentCount} utilisateur(s).\n";

} catch (Exception $e) {
    echo "Erreur d'exécution : " . $e->getMessage() . "\n";
}