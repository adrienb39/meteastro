<?php

namespace App\UserStory;

use App\Entity\UserAdmin;
use Doctrine\ORM\EntityManager;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class LoginUser
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(string $email, ?string $temporaryCode = null, ?string $password = null, string $confirmPassword = null): UserAdmin
    {
        // Recherche de l'utilisateur par email
        $user = $this->entityManager->getRepository(UserAdmin::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new Exception("Utilisateur non trouvé.");
        }

        // Étape 1 : Si un code temporaire est fourni, on le vérifie
        if ($temporaryCode && !$password) {  // Vérification du code temporaire uniquement si le mot de passe n'est pas fourni
            if ($this->verifyTemporaryCode($user, $temporaryCode)) {
                // Code temporaire valide, on passe à l'étape suivante pour définir le mot de passe
                return $user;
            } else {
                throw new Exception("Code temporaire invalide.");
            }
        }

        // Si l'utilisateur n'a pas de mot de passe et qu'il n'a pas déjà un code temporaire valide
        if (!$user->getPassword() && (!$user->getTemporaryCode() || $user->getCodeExpiration() < new \DateTime())) {
            // Si aucun mot de passe n'est fourni et que l'utilisateur n'a pas de code temporaire valide ou il est expiré
            $this->generateTemporaryCode($user);  // Générer un code temporaire pour l'utilisateur
            return $user;  // Retourner l'utilisateur après la génération du code temporaire
        }

        // Étape 2 : Si un mot de passe est fourni, on le valide et on l'enregistre
        if ($password) {
            // Vérification du mot de passe (si l'utilisateur veut se connecter)
            if (!$confirmPassword) {  // Si confirmPassword n'est pas fourni, l'utilisateur essaie de se connecter
                if (!$this->verifyPassword($user, $password)) {
                    throw new Exception("Mot de passe incorrect.");
                }
                return $user;  // Connexion réussie
            }

            // Étape de réinitialisation ou de définition d'un nouveau mot de passe
            if ($password !== $confirmPassword) {
                throw new Exception("Les mots de passe ne correspondent pas.");
            }

            if (!$this->isPasswordValid($password)) {
                throw new Exception("Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre, un caractère spécial, et avoir au moins 12 caractères.");
            }

            // Enregistrer le nouveau mot de passe
            $this->createPassword($user, $password);
            return $user;
        }
        return $user;  // Retourner l'utilisateur après la génération du code temporaire
    }

    private function verifyPassword(UserAdmin $user, string $password): UserAdmin
    {
        // Vérification du mot de passe enregistré
        if (password_verify($password, $user->getPassword())) {
            $user->setTemporaryCode(null);
            $user->setCodeExpiration(null);
            $this->entityManager->persist($user);  // Enregistrer l'utilisateur avec le nouveau mot de passe
            $this->entityManager->flush();  // Sauvegarder les changements dans la base de données
            return $user;  // Connexion réussie
        } else {
            throw new Exception("Mot de passe incorrect.");
        }
    }

    public function verifyTemporaryCode(UserAdmin $user, string $temporaryCode, string $password = null): bool
    {
        if ($password === null && $temporaryCode) {
            // Vérifier le code temporaire uniquement
            if ($user->getTemporaryCode() === $temporaryCode) {
                return true;  // Code temporaire valide
            } else {
                throw new Exception("Code temporaire invalide.");
            }
        }

        // Le code temporaire est valide si le mot de passe est vérifié également (lors de la création du mot de passe)
        if ($password && $this->verifyPassword($user, $password)) {
            return true;
        }

        return false;
    }

    public function createPassword(UserAdmin $user, string $password): void
    {
        // Hachage du mot de passe avant de l'enregistrer
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Mise à jour du mot de passe de l'utilisateur
        $user->setPassword($hashedPassword);
        $user->setTemporaryCode(null);
        $user->setCodeExpiration(null);
        $user->setIsActive(true);
        $this->entityManager->persist($user);  // Enregistrer l'utilisateur avec le nouveau mot de passe
        $this->entityManager->flush();  // Sauvegarder les changements dans la base de données
    }

    public function generateTemporaryCode(UserAdmin $user): void
    {
        // Génération d'un code temporaire aléatoire
        $temporaryCode = bin2hex(random_bytes(6));  // Code de 12 caractères hexadécimaux
        $expirationDate = new \DateTime('+1 hour');  // Code valide pendant 1 heure
        $expirationDate->setTimezone(new \DateTimeZone('Europe/Paris'));

        // Affectation du code temporaire à l'utilisateur
        $user->setTemporaryCode($temporaryCode);
        $user->setCodeExpiration($expirationDate);

        // Sauvegarde en base de données
        $this->entityManager->flush();

        // Envoi de l'email avec le code temporaire
        $this->sendTemporaryCodeByEmail($user->getEmail(), $temporaryCode);
    }

    private function sendTemporaryCodeByEmail(string $email, string $temporaryCode): void
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP (par exemple, avec Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = 'dvmta39@gmail.com'; // Votre adresse email
            $mail->Password = 'pnnikshkztituxfj';    // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPDebug = 2;  // Affiche les détails de la communication SMTP

            // Destinataire et expéditeur
            $mail->setFrom('dvmta39@gmail.com', 'Espace membres du bureau de AVVA39');
            $mail->addAddress($email);  // L'email de l'utilisateur

            // Sujet et contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Votre code temporaire pour l\'espace membres du bureau de AVVA39';
            $mail->Body = "Voici votre code temporaire valide 1h : <strong>$temporaryCode</strong>.<br>Après 1h, si vous n'avez pas créer votre mot de passe, veuillez contacter l'administrateur.";

            // Envoi de l'email
            $mail->send();
        } catch (Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    private function isPasswordValid(string $password): bool
    {
        // Vérification des critères du mot de passe
        return preg_match('/[a-z]/', $password) &&  // au moins une minuscule
            preg_match('/[A-Z]/', $password) &&  // au moins une majuscule
            preg_match('/\d/', $password) &&     // au moins un chiffre
            preg_match('/[\W_]/', $password) &&  // au moins un caractère spécial
            strlen($password) >= 8;             // minimum 8 caractères
    }
}