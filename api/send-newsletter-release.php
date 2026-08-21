<?php
// send-newsletter.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envoie la newsletter de mise à jour uniquement aux utilisateurs abonnés
 * n'ayant pas encore reçu la notification pour la version $latestVersion.
 *
 * @param object $obj Instance de connexion à la base de données (Db)
 * @param string $latestVersion Numéro de version (ex: '2.5.0')
 * @param string $logoPath Chemin physique du logo
 * @return array Statistiques d'envoi
 */
function sendNewsletterRelease($obj, $latestVersion, $logoPath)
{
    // 1. Récupération des abonnés n'ayant pas encore reçu cette version
    $sql = "SELECT `id_users`, `email`, `name` 
            FROM `users` 
            WHERE `newsletter` = 1 
              AND (`last_version` IS NULL OR `last_version` != ?)";

    $subscribers = $obj->query2($sql, [$latestVersion]);

    if (empty($subscribers)) {
        return [
            'sent_count' => 0,
            'total' => 0,
            'errors' => []
        ];
    }

    $logoExists = file_exists($logoPath);

    $sentCount = 0;
    $errors = [];

    $totalInBatch = count($subscribers);
    $index = 0;

    // 2. Boucle d'envoi des e-mails
    foreach ($subscribers as $user) {
        $userId = $user['id_users'];
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
            $mail->SMTPDebug = 0;

            // Destinataire
            $mail->setFrom('dvmta39@gmail.com', 'Meteastro - Station de Contrôle');
            $mail->addAddress($userEmail, $userName);
            $mail->addReplyTo('dvmta39@gmail.com', 'Support Meteastro');

            // Intégration du logo
            if (file_exists($logoExists)) {
                $mail->addEmbeddedImage($logoPath, 'meteastro_logo');
                $logoSrc = 'cid:meteastro_logo';
            } else {
                $logoSrc = 'https://meteastro.fr/ressources/logo.png';
            }

            $pseudo = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
            $emailEscaped = htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8');

            $mail->isHTML(true);
            $mail->Subject = "🚀 [METEASTRO] Le site est en ligne ! Découvrez la v{$latestVersion}";

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
                            <h2 style='font-size: 20px; color: #38bdf8; margin-top: 0;'>Transmission spéciale pour : {$pseudo}</h2>
                            
                            <p style='color: #e2e8f0; line-height: 1.6; font-size: 14px;'>
                                La plateforme Meteastro est désormais <strong>officiellement en ligne</strong> dans sa version <strong style='color: #38bdf8;'>v{$latestVersion}</strong> !
                            </p>

                            <div style='background: #020617; border: 1px solid #334155; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                                <h3 style='font-size: 15px; color: #f59e0b; margin-top: 0; text-transform: uppercase;'>
                                    ⚡ Changements apportés dans cette version :
                                </h3>
                                <ul style='margin: 0; padding-left: 20px; color: #cbd5e1; font-size: 13px; line-height: 1.8;'>
                                    <li><strong style='color: #f1f5f9;'>Amélioration :</strong> Termes et conditions à coché</li>
                                </ul>
                            </div>

                            <div align='center' style='margin: 30px 0 15px 0;'>
                                <a href='https://meteastro.fr' style='background-color: #0284c7; color: #ffffff; padding: 12px 28px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 25px; display: inline-block;'>
                                    Visiter le site Meteastro
                                </a>
                            </div>

                            <p style='margin-top: 25px; font-size: 12px; color: #64748b; text-align: center;'>
                                Vous recevez cet e-mail car vous êtes abonné à la newsletter Meteastro ({$emailEscaped}).
                            </p>
                        </td>
                    </tr>
                </table>
            </div>";

            // Envoi de l'e-mail
            $mail->send();
            $sentCount++;

            // Mise à jour de la version enregistrée pour cet utilisateur
            $updateSql = "UPDATE `users` SET `last_version` = ? WHERE `id_users` = ?";
            $obj->query2($updateSql, [$latestVersion, $userId]);

        } catch (Exception $e) {
            $errors[] = "Erreur pour {$userEmail} : " . $e->getMessage();
        }
        if ($index < $totalInBatch) {
            usleep(300000); // 0.3 seconde
        }
    }

    return [
        'sent_count' => $sentCount,
        'total' => count($subscribers),
        'errors' => $errors
    ];
}