<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Service responsable de l'envoi des e-mails de code (vérification
 * d'inscription et réinitialisation de mot de passe), sur le même
 * gabarit visuel que les autres communications Meteastro.
 */
class MailService
{
    private string $host;
    private string $user;
    private string $pass;
    private string $fromName;
    private string $logoPath;
    private string $logoUrl;

    public function __construct()
    {
        // Idéalement passés via $_ENV / .env (voir commentaire dans
        // AstronomieController::contactProcess sur SMTP_PASS).
        $this->host     = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->user     = getenv('SMTP_USER') ?: 'dvmta39@gmail.com';
        $this->pass     = getenv('SMTP_PASS') ?: 'pnnikshkztituxfj';
        $this->fromName = getenv('SMTP_FROM_NAME') ?: 'Meteastro - Station de Contrôle';
        $this->logoPath = __DIR__ . '/../../public/assets/images/logo.png';
        $this->logoUrl  = 'https://meteastro.fr/assets/images/logo.png';
    }

    /**
     * @param string $type 'verification' | 'reset'
     */
    public function sendCodeEmail(string $userEmail, string $userName, string $code, string $type): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->user;
            $mail->Password = $this->pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;

            $mail->setFrom($this->user, $this->fromName);
            $mail->addAddress($userEmail, $userName);
            $mail->addReplyTo($this->user, 'Support Meteastro');

            if (file_exists($this->logoPath)) {
                $mail->addEmbeddedImage($this->logoPath, 'meteastro_logo');
                $logoSrc = 'cid:meteastro_logo';
            } else {
                $logoSrc = $this->logoUrl;
            }

            $pseudo = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
            $emailEscaped = htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8');
            $codeEscaped = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');

            $mail->isHTML(true);

            if ($type === 'reset') {
                $mail->Subject = "🔑 [METEASTRO] Votre code de réinitialisation de mot de passe";
                $title = "Réinitialisation de mot de passe";
                $intro = "vous avez demandé la réinitialisation de votre mot de passe. Voici votre code de vérification :";
                $footerNote = "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet e-mail.";
            } else {
                $mail->Subject = "🚀 [METEASTRO] Confirmez votre adresse e-mail";
                $title = "Bienvenue sur Meteastro";
                $intro = "merci pour votre inscription ! Voici votre code de vérification pour activer votre compte :";
                $footerNote = "Si vous n'êtes pas à l'origine de cette inscription, ignorez simplement cet e-mail.";
            }

            $mail->Body = $this->buildBody($title, $pseudo, $intro, $codeEscaped, $footerNote, $emailEscaped, $logoSrc);

            $mail->send();
            return true;
        } catch (PHPMailerException $e) {
            error_log('Erreur envoi mail Meteastro (' . $type . ') : ' . $mail->ErrorInfo);
            return false;
        }
    }

    private function buildBody(
        string $title,
        string $pseudo,
        string $intro,
        string $codeEscaped,
        string $footerNote,
        string $emailEscaped,
        string $logoSrc
    ): string {
        return "
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
                        <h2 style='font-size: 20px; color: #38bdf8; margin-top: 0;'>{$title}</h2>
                        <p style='color: #e2e8f0; line-height: 1.6; font-size: 14px;'>
                            Bonjour {$pseudo}, {$intro}
                        </p>
                        <div align='center' style='background: #020617; border: 1px solid #334155; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <span style='font-size: 28px; letter-spacing: 6px; font-weight: bold; color: #f59e0b;'>{$codeEscaped}</span>
                        </div>
                        <p style='color: #94a3b8; font-size: 12px; line-height: 1.6;'>{$footerNote}</p>
                        <p style='margin-top: 25px; font-size: 12px; color: #64748b; text-align: center;'>
                            Cet e-mail a été envoyé à {$emailEscaped}.
                        </p>
                    </td>
                </tr>
            </table>
        </div>";
    }
}