<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Throwable;

class NewsletterMailerService
{
    private const BATCH_SIZE = 30;

    private EntityManagerInterface $entityManager;
    private string $projectDir;

    public function __construct(EntityManagerInterface $entityManager, string $projectDir)
    {
        $this->entityManager = $entityManager;
        $this->projectDir = $projectDir;
    }

    /**
     * Traite un lot d'envoi de la newsletter.
     */
    public function sendReleaseNewsletter(string $latestVersion): array
    {
        $logFile = $this->projectDir . '/logs/newsletter-cron.log';
        $logoPath = $this->projectDir . '/public/ressources/logo.png';

        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var User[] $pendingUsers */
        $pendingUsers = $userRepository->createQueryBuilder('u')
            ->where('u.newsletter = :newsletter')
            ->andWhere('u.lastVersion IS NULL OR u.lastVersion != :latestVersion')
            ->setParameter('newsletter', true)
            ->setParameter('latestVersion', $latestVersion)
            ->setMaxResults(self::BATCH_SIZE)
            ->getQuery()
            ->getResult();

        $totalPendingCount = (int) $userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.newsletter = :newsletter')
            ->andWhere('u.lastVersion IS NULL OR u.lastVersion != :latestVersion')
            ->setParameter('newsletter', true)
            ->setParameter('latestVersion', $latestVersion)
            ->getQuery()
            ->getSingleScalarResult();

        $this->logCron(
            sprintf("Vérification v%s : %d abonné(s) en attente (lot max : %d).", $latestVersion, $totalPendingCount, self::BATCH_SIZE),
            $logFile
        );

        $sentCount = 0;
        $errors = [];
        $totalInBatch = count($pendingUsers);
        $logoExists = file_exists($logoPath);

        if (!empty($pendingUsers)) {
            foreach ($pendingUsers as $index => $user) {
                $userEmail = filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL);
                $userName = $user->getName() ? trim($user->getName()) : 'Astronome';

                if (!$userEmail) {
                    $errors[] = "E-mail invalide pour l'utilisateur ID {$user->getId()}";
                    continue;
                }

                try {
                    $mail = new PHPMailer(true);

                    // Configuration SMTP Hostinger
                    $mail->isSMTP();
                    $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.hostinger.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['SMTP_USER'] ?? 'dvmta39@gmail.com';
                    $mail->Password = $_ENV['SMTP_PASS'] ?? 'pnnikshkztituxfj';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    $mail->SMTPDebug = 0;

                    $senderEmail = $_ENV['SMTP_USER'] ?? 'dvmta39@gmail.com';
                    $mail->setFrom($senderEmail, 'Meteastro - Station de Contrôle');
                    $mail->addAddress($userEmail, $userName);
                    $mail->addReplyTo($senderEmail, 'Support Meteastro');

                    if ($logoExists) {
                        $mail->addEmbeddedImage($logoPath, 'meteastro_logo');
                        $logoSrc = 'cid:meteastro_logo';
                    } else {
                        $logoSrc = 'https://meteastro.fr/assets/images/logo.png';
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
                                            <li><strong style='color: #f1f5f9;'>Correction :</strong> Affichage du contenu</li>
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

                    $mail->send();
                    $sentCount++;

                    $user->setLastVersion($latestVersion);

                } catch (MailException $mailException) {
                    $errors[] = "Erreur pour {$userEmail} : " . $mailException->getMessage();
                } catch (Throwable $t) {
                    $errors[] = "Erreur inattendue pour {$userEmail} : " . $t->getMessage();
                }

                if ($index < $totalInBatch - 1) {
                    usleep(300000);
                }
            }

            $this->entityManager->flush();

            $this->logCron(sprintf(
                "Lot terminé : %d/%d envoyé(s), %d erreur(s), %d restant(s).",
                $sentCount,
                $totalInBatch,
                count($errors),
                max(0, $totalPendingCount - $sentCount)
            ), $logFile);
        }

        return [
            'sent_count' => $sentCount,
            'total_pending' => $totalPendingCount,
            'batch_size' => self::BATCH_SIZE,
            'errors' => $errors,
        ];
    }

    private function logCron(string $message, string $logFile): void
    {
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $line = '[' . $timestamp->format('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        file_put_contents($logFile, $line, FILE_APPEND);
    }
}