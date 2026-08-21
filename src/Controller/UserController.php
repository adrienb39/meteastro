<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailService;
use Doctrine\ORM\EntityManager;

class UserController extends AbstractController
{
    private EntityManager $entityManager;
    private MailService $mailService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mailService = new MailService();
    }

    private function isConnected(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return isset($_SESSION['email']) && isset($_SESSION['password']);
    }

    // -----------------------------------------------------------------
    // Connexion
    // -----------------------------------------------------------------
    public function login(): void
    {
        $isConnected = $this->isConnected();
        $errors = [];
        $email = '';

        if (!$isConnected && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $errors['email'] = "Il semblerait que vous ne soyez pas encore membre ! Cliquez sur le lien du bas pour vous inscrire.";
            } elseif (!password_verify($password, $user->getPassword())) {
                $errors['email'] = "Courriel ou mot de passe incorrect !";
            } else {
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['name'] = $user->getName();
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['password'] = $user->getPassword();

                if ($user->getStatus() === 'verified') {
                    header('Location: /index.php');
                    exit();
                }

                // Compte pas encore vérifié : on renvoie un nouveau code par e-mail
                $code = (string) random_int(111111, 999999);
                $user->setCode($code);
                $this->entityManager->flush();

                $mailSent = $this->mailService->sendCodeEmail($user->getEmail(), $user->getName(), $code, 'verification');

                $_SESSION['info'] = $mailSent
                    ? "Il semble que vous n'ayez pas encore vérifié votre adresse e-mail. Un nouveau code vient de vous être envoyé - {$email}"
                    : "Il semble que vous n'ayez pas encore vérifié votre adresse e-mail, et l'envoi du code a échoué. Réessayez plus tard.";
                header('Location: user-otp.php');
                exit();
            }
        }

        $this->render('connexion/login', [
            'hideSiteHeader' => true,
            'isConnected' => $isConnected,
            'errors' => $errors,
            'email' => $email,
        ]);
    }

    // -----------------------------------------------------------------
    // Inscription
    // -----------------------------------------------------------------
    public function signup(): void
    {
        $isConnected = $this->isConnected();
        $errors = [];
        $name = '';
        $email = '';

        if (!$isConnected && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $cpassword = (string) ($_POST['cpassword'] ?? '');

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

            if (count($errors) === 0) {
                $existing = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existing) {
                    $errors['email'] = "L'email que vous avez saisi existe déjà !";
                }
            }

            if (count($errors) === 0) {
                $user = new User();
                $user->setName($name);
                $user->setEmail($email);
                $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
                $user->setCode((string) random_int(111111, 999999));
                $user->setStatus('notverified');

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mailSent = $this->mailService->sendCodeEmail($user->getEmail(), $user->getName(), $user->getCode(), 'verification');

                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                $_SESSION['info'] = $mailSent
                    ? "Un code de vérification vous a été envoyé par e-mail."
                    : "Votre compte a été créé, mais l'e-mail de vérification n'a pas pu être envoyé. Contactez le support si le problème persiste.";
                $_SESSION['email'] = $user->getEmail();
                $this->redirect('/connexion/user-otp');
                exit();
            }
        }

        $this->render('connexion/signup', [
            'hideSiteHeader' => true,
            'isConnected' => $isConnected,
            'errors' => $errors,
            'name' => $name,
            'email' => $email,
        ]);
    }

    // -----------------------------------------------------------------
    // Vérification du code d'inscription
    // -----------------------------------------------------------------
    public function checkOtp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check'])) {
            $_SESSION['info'] = "";
            $otpCode = (string) ($_POST['otp'] ?? '');

            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['code' => $otpCode]);

            if (!$user) {
                $errors['otp-error'] = "Vous avez saisi un code incorrect !";
            } else {
                $user->setCode('0');
                $user->setStatus('verified');
                $this->entityManager->flush();

                $email = $user->getEmail();
                $_SESSION['email'] = $email;
                session_destroy();
                session_start();
                $_SESSION['info'] = "Vous pouvez maintenant vous connecter à votre compte.";
                $this->redirect('/connexion/login');
                exit();
            }
        }

        $this->render('connexion/user-otp', [
            'hideSiteHeader' => true,
            'errors' => $errors,
        ]);
    }

    // -----------------------------------------------------------------
    // Mot de passe oublié - étape 1 : saisie de l'e-mail
    // -----------------------------------------------------------------
    public function forgotPassword(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $errors = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check-email'])) {
            $email = trim((string) ($_POST['email'] ?? ''));

            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $errors['email'] = "Cette adresse e-mail n'existe pas !";
            } else {
                $code = (string) random_int(111111, 999999);
                $user->setCode($code);
                $this->entityManager->flush();

                $mailSent = $this->mailService->sendCodeEmail($user->getEmail(), $user->getName(), $code, 'reset');

                $_SESSION['info'] = $mailSent
                    ? "Un code de réinitialisation vous a été envoyé par e-mail."
                    : "Le code a été généré, mais l'e-mail n'a pas pu être envoyé. Contactez le support si le problème persiste.";
                $_SESSION['email'] = $email;
                $this->redirect('/connexion/reset-code');
                exit();
            }
        }

        $this->render('connexion/forgot-password', [
            'hideSiteHeader' => true,
            'errors' => $errors,
            'email' => $email,
        ]);
    }

    // -----------------------------------------------------------------
    // Mot de passe oublié - étape 2 : vérification du code
    // -----------------------------------------------------------------
    public function checkResetOtp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check-reset-otp'])) {
            $_SESSION['info'] = "";
            $otpCode = (string) ($_POST['otp'] ?? '');

            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['code' => $otpCode]);

            if (!$user) {
                $errors['otp-error'] = "Vous avez saisi un code incorrect !";
            } else {
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['info'] = "Veuillez créer un nouveau mot de passe que vous n'utilisez sur aucun autre site.";
                $this->redirect('/connexion/new-password');
                exit();
            }
        }

        $this->render('connexion/reset-code', [
            'hideSiteHeader' => true,
            'errors' => $errors,
        ]);
    }

    // -----------------------------------------------------------------
    // Mot de passe oublié - étape 3 : changement du mot de passe
    // -----------------------------------------------------------------
    public function changePassword(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change-password'])) {
            $_SESSION['info'] = "";
            $password = (string) ($_POST['password'] ?? '');
            $cpassword = (string) ($_POST['cpassword'] ?? '');

            if ($password !== $cpassword) {
                $errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
            } else {
                $email = $_SESSION['email'] ?? '';
                /** @var User|null $user */
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user) {
                    $user->setCode('0');
                    $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
                    $this->entityManager->flush();

                    $_SESSION['info'] = "Votre mot de passe a changé. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
                    header('Location: password-changed.php');
                    exit();
                }

                $errors['db-error'] = "Le changement de mot de passe a échoué !";
            }
        }

        $this->render('connexion/new-password', [
            'errors' => $errors,
        ]);
    }
}