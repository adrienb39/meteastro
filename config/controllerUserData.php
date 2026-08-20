<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use function Safe\session_destroy;

require __DIR__ . '/../vendor/autoload.php'; // Autoload Composer (PHPMailer, Safe, etc.)
session_start();
require "connexion_bdd.php"; // Assurez-vous que ce fichier contient la fonction createMysqliConnection()

$dbType = 'mysqli';

if ($dbType === 'pdo') {
    $db = createPdoConnection();
} else {
    $mysqli = createMysqliConnection(); // Créez une connexion MySQLi
}

// ---------------------------------------------------------------------
// Configuration SMTP
// À terme, sortez ces valeurs de ce fichier (variables d'environnement /
// fichier .env non versionné) plutôt que de les laisser en dur ici.
// ---------------------------------------------------------------------
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'dvmta39@gmail.com');
define('SMTP_PASS', 'pnnikshkztituxfj');
define('SMTP_FROM_NAME', 'Meteastro - Station de Contrôle');
define('SMTP_LOGO_PATH', __DIR__ . '/../ressources/logo.png');
define('SMTP_LOGO_URL', 'https://meteastro.fr/ressources/logo.png');

/**
 * Envoie un e-mail contenant un code (vérification d'inscription ou
 * réinitialisation de mot de passe), sur le même gabarit visuel que les
 * autres communications Meteastro.
 *
 * @param string $userEmail Adresse du destinataire
 * @param string $userName  Nom affiché du destinataire
 * @param string $code      Code à afficher dans l'e-mail
 * @param string $type      'verification' | 'reset'
 * @return bool  true si l'envoi a réussi, false sinon
 */
function sendCodeEmail(string $userEmail, string $userName, string $code, string $type): bool
{
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0;

        // Expéditeur / destinataire
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($userEmail, $userName);
        $mail->addReplyTo(SMTP_USER, 'Support Meteastro');

        // Logo intégré si présent localement, sinon URL distante
        if (file_exists(SMTP_LOGO_PATH)) {
            $mail->addEmbeddedImage(SMTP_LOGO_PATH, 'meteastro_logo');
            $logoSrc = 'cid:meteastro_logo';
        } else {
            $logoSrc = SMTP_LOGO_URL;
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

        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        error_log('Erreur envoi mail Meteastro (' . $type . ') : ' . $mail->ErrorInfo);
        return false;
    }
}

// Variables
$email = "";
$name = "";
$errors = array();

// ---------------------------------------------------------------------
// Inscription
// ---------------------------------------------------------------------
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Validation des entrées
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'adresse électronique n'est pas valide !";
    }
    if (strlen($password) < 8) {
        $errors['password'] = "La longueur du mot de passe doit être d'au moins 8 caractères !";
    }
    $pattern = '/^(?=.*\d)(?=.*[A-Za-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
    if (!preg_match($pattern, $password)) {
        $errors['password'] = "Le mot de passe doit contenir au moins un chiffre, une lettre majuscule, une lettre minuscule et un caractère spécial.";
    }
    if ($password !== $cpassword) {
        $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
    }
    if (!isset($_POST['consent'])) {
        $errors[] = "Vous devez accepter les termes et conditions.";
    }

    // Vérification de l'email
    if (count($errors) === 0) {
        $email_check = "SELECT * FROM users WHERE email = ?";
        $stmt = $mysqli->prepare($email_check);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors['email'] = "L'email que vous avez saisi existe déjà !";
        }
    }

    // Si pas d'erreurs, insérer l'utilisateur
    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = (string) random_int(111111, 999999); // Générer le code de vérification
        $status = "notverified";

        // Insérer les données dans la base
        $insert_data = "INSERT INTO users (name, email, password, code, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_data);
        $stmt->bind_param("sssss", $name, $email, $encpass, $code, $status);
        $data_check = $stmt->execute();

        if ($data_check) {
            // Envoi du code de vérification par e-mail (au lieu de l'afficher)
            $mail_sent = sendCodeEmail($email, $name, $code, 'verification');

            if ($mail_sent) {
                $_SESSION['info'] = "Un code de vérification vous a été envoyé par e-mail.";
            } else {
                $_SESSION['info'] = "Votre compte a été créé, mais l'e-mail de vérification n'a pas pu être envoyé. Contactez le support si le problème persiste.";
            }
            $_SESSION['email'] = $email;
            header('location: user-otp.php');
            exit();
        } else {
            $errors['db-error'] = "Échec lors de l'insertion de données dans la base de données !";
        }
    }
}

// ---------------------------------------------------------------------
// Vérification du code d'inscription
// ---------------------------------------------------------------------
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code = "SELECT * FROM users WHERE code = ?";
    $stmt = $mysqli->prepare($check_code);
    $stmt->bind_param("s", $otp_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetch_data = $result->fetch_assoc();
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'verified';

        // Mettre à jour le code et le statut
        $update_otp = "UPDATE users SET code = ?, status = ? WHERE code = ?";
        $stmt = $mysqli->prepare($update_otp);
        $stmt->bind_param("ssi", $code, $status, $fetch_code);
        $update_res = $stmt->execute();

        if ($update_res) {
            $_SESSION['email'] = $email;
            session_destroy();
            session_start();
            $_SESSION['info'] = "Vous pouvez maintenant vous connecter à votre compte.";
            header('location: /connexion/login.php');
            exit();
        } else {
            $errors['otp-error'] = "Échec lors de la mise à jour du code !";
        }
    } else {
        $errors['otp-error'] = "Vous avez saisi un code incorrect !";
    }
}

// ---------------------------------------------------------------------
// Connexion
// ---------------------------------------------------------------------
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetch = $result->fetch_assoc();
        $fetch_pass = $fetch['password'];
        if (password_verify($password, $fetch_pass)) {
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $fetch['name'];
            $_SESSION['newsletter'] = $fetch['newsletter'] ?? 0;
            $_SESSION['user_id'] = $fetch['id_users'];
            $status = $fetch['status'];
            if ($status == 'verified') {
                header('location: /index.php');
            } else {
                // Compte pas encore vérifié : on renvoie un nouveau code par e-mail
                $code = (string) random_int(111111, 999999);
                $update_code = "UPDATE users SET code = ? WHERE email = ?";
                $stmt = $mysqli->prepare($update_code);
                $stmt->bind_param("ss", $code, $email);
                $stmt->execute();

                $mail_sent = sendCodeEmail($email, $fetch['name'], $code, 'verification');

                $info = $mail_sent
                    ? "Il semble que vous n'ayez pas encore vérifié votre adresse e-mail. Un nouveau code vient de vous être envoyé - $email"
                    : "Il semble que vous n'ayez pas encore vérifié votre adresse e-mail, et l'envoi du code a échoué. Réessayez plus tard.";
                $_SESSION['info'] = $info;
                header('location: user-otp.php');
            }
        } else {
            $errors['email'] = "Courriel ou mot de passe incorrect !";
        }
    } else {
        $errors['email'] = "Il semblerait que vous ne soyez pas encore membre ! Cliquez sur le lien du bas pour vous inscrire.";
    }
}

// ---------------------------------------------------------------------
// Mot de passe oublié - étape 1 : saisie de l'e-mail
// ---------------------------------------------------------------------
if (isset($_POST['check-email'])) {
    $email = $_POST['email'];
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetch_data = $result->fetch_assoc();
        $code = (string) random_int(111111, 999999);
        $insert_code = "UPDATE users SET code = ? WHERE email = ?";
        $stmt = $mysqli->prepare($insert_code);
        $stmt->bind_param("ss", $code, $email);
        $run_query = $stmt->execute();

        if ($run_query) {
            // Envoi du code de réinitialisation par e-mail (au lieu de l'afficher)
            $mail_sent = sendCodeEmail($email, $fetch_data['name'], $code, 'reset');

            if ($mail_sent) {
                $_SESSION['info'] = "Un code de réinitialisation vous a été envoyé par e-mail.";
            } else {
                $_SESSION['info'] = "Le code a été généré, mais l'e-mail n'a pas pu être envoyé. Contactez le support si le problème persiste.";
            }
            $_SESSION['email'] = $email;
            header('location: reset-code.php');
            exit();
        } else {
            $errors['db-error'] = "Quelque chose n'a pas fonctionné !";
        }
    } else {
        $errors['email'] = "Cette adresse e-mail n'existe pas !";
    }
}

// ---------------------------------------------------------------------
// Mot de passe oublié - étape 2 : vérification du code
// ---------------------------------------------------------------------
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code = "SELECT * FROM users WHERE code = ?";
    $stmt = $mysqli->prepare($check_code);
    $stmt->bind_param("s", $otp_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetch_data = $result->fetch_assoc();
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Veuillez créer un nouveau mot de passe que vous n'utilisez sur aucun autre site.";
        $_SESSION['info'] = $info;
        header('location: new-password.php');
        exit();
    } else {
        $errors['otp-error'] = "Vous avez saisi un code incorrect !";
    }
}

// ---------------------------------------------------------------------
// Mot de passe oublié - étape 3 : changement du mot de passe
// ---------------------------------------------------------------------
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    if ($password !== $cpassword) {
        $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
    } else {
        $code = 0;
        $email = $_SESSION['email']; // Récupération de cet email via la session
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $update_pass = "UPDATE users SET code = ?, password = ? WHERE email = ?";
        $stmt = $mysqli->prepare($update_pass);
        $stmt->bind_param("iss", $code, $encpass, $email);
        $run_query = $stmt->execute();

        if ($run_query) {
            $info = "Votre mot de passe a changé. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
            $_SESSION['info'] = $info;
            header('Location: password-changed.php');
        } else {
            $errors['db-error'] = "Le changement de mot de passe a échoué !";
        }
    }
}

// ---------------------------------------------------------------------
// Redirection manuelle vers la page de connexion
// ---------------------------------------------------------------------
if (isset($_POST['login-now'])) {
    header('Location: login.php');
    exit();
}