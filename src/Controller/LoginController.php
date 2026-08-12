<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Role;
use App\Entity\UserAdmin;
use App\UserStory\CreerUtilisateur;
use App\UserStory\LoginUser;
use App\UserStory\ModifierUtilisateur;
use Doctrine\ORM\EntityManager;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class LoginController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        $this->render('account/login');
    }

    public function login(): void
    {
        session_start();

        if (isset($_GET['reset']) && $_GET['reset'] === '1') {
            // Suppression des variables de session spécifiques à la connexion en cours
            unset($_SESSION['step']);
            unset($_SESSION['user_id']);
            unset($_SESSION['user_email']);
            unset($_SESSION['user_role_id']);
            unset($_SESSION['user_role_nom']);
            unset($_SESSION['user']);
            unset($_SESSION['password']);

            // Supprimer les messages pour repartir à zéro
            unset($_SESSION['error_message']);
            unset($_SESSION['success_message']);

            // Rediriger pour nettoyer l'URL et éviter la répétition du reset
            $this->redirect("/avva-admin/login");
            return; // Arrêter l'exécution après la redirection
        }

        // Vérification du formulaire soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST["email_user"] ?? '';
            $password = $_POST["password_user"] ?? '';
            $temporaryCode = $_POST["code_user"] ?? '';
            $confirmPassword = $_POST["confirm_password"] ?? '';

            try {
                // Étape 1 : Vérification de l'email
                if (empty($_SESSION['step']) || $_SESSION['step'] === 'email') {
                    $loginUser = new LoginUser($this->entityManager);
                    $user = $loginUser->execute($email, null, null, null);  // Vérification de l'email uniquement

                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_email'] = $user->getEmail();

                    $_SESSION['user_role_id'] = $user->getRole()->getId();
                    $_SESSION['user_role_nom'] = $user->getRole()->getNom();

                    if (isset($_SESSION['user_id'])) {
                        $user = $this->entityManager->getRepository(UserAdmin::class)->find($_SESSION['user_id']);
                    }

                    $_SESSION['user'] = [
                        'id' => $_SESSION['user_id'],
                        'email' => $_SESSION['user_email'],
                        'admin' => $user->getAdmin(),
                        'active' => $user->isActive(),
                        'idRole' => $_SESSION['user_role_id'],
                        'nomRole' => $_SESSION['user_role_nom']
                    ];

                    if ($user->getPassword() && !$user->getTemporaryCode()) {
                        $_SESSION['step'] = 'password'; // Passer à l'étape du mot de passe
                    } else {
                        $_SESSION['success_message'] = "Vous allez recevoir un code temporaire envoyé par mail.";
                        $_SESSION['step'] = 'temporary_code'; // Passer à l'étape du code temporaire
                    }

                    $this->redirect("/avva-admin/login");
                }

                // Étape 2 : Vérification du mot de passe
                if ($_SESSION['step'] === 'password') {
                    $user = $_SESSION['user'];
                    $loginUser = new LoginUser($this->entityManager);

                    $loginUser->execute($email, null, $password, null);  // Vérification du mot de passe

                    $_SESSION['password'] = $password;

                    $_SESSION['success_message'] = "Connexion réussie !";
                    $this->redirect("/avva-admin/accueil");
                }

                // Étape 3 : Vérification du code temporaire
                if ($_SESSION['step'] === 'temporary_code') {
                    $user = $_SESSION['user'];
                    $loginUser = new LoginUser($this->entityManager);

                    try {
                        $loginUser->execute($email, $temporaryCode);  // Passer uniquement le code temporaire
                        $_SESSION['success_message'] = "Code temporaire validé. Vous pouvez maintenant créer un mot de passe.";
                        $_SESSION['step'] = 'create_password';  // Passer à l'étape de création du mot de passe
                        $this->redirect("/avva-admin/login");
                    } catch (Exception $e) {
                        $_SESSION['error_message'] = $e->getMessage();
                        $this->redirect("/avva-admin/login");
                    }
                }

                // Étape 4 : Création du mot de passe
                if ($_SESSION['step'] === 'create_password') {
                    // Vérifier si les mots de passe correspondent
                    if ($password !== $confirmPassword) {
                        $_SESSION['error_message'] = "Les mots de passe ne correspondent pas.";
                        $this->redirect("/avva-admin/login");
                    }

                    // Créer et enregistrer le mot de passe (hacher et sauvegarder)
                    $user = $_SESSION['user'];
                    $loginUser = new LoginUser($this->entityManager);

                    try {
                        // Appeler la méthode qui va valider et enregistrer le mot de passe
                        $loginUser->execute($email, null, $password, $confirmPassword);  // Passer le mot de passe à cette étape
                        session_unset();
                        session_destroy();
                        session_start();
                        $_SESSION['success_message'] = "Mot de passe créé avec succès. Vous pouvez vous connecter";
                        $this->redirect("/avva-admin/login"); // Rediriger après création réussie
                    } catch (Exception $e) {
                        $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
                        $this->redirect("/avva-admin/login");
                    }
                }


            } catch (Exception $e) {
                $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
            }
        }

        $_SESSION['isUserConnectedLogin'] = !isset($_SESSION['user']) || !isset($_SESSION['password']) || !$_SESSION['user']['active'] == true;

        $this->render('admin/account/login', [
            'errorMessage' => $_SESSION['error_message'] ?? null,
            'successMessage' => $_SESSION['success_message'] ?? null,
            'step' => $_SESSION['step'] ?? 'email',
            'isUserConnected' => $_SESSION['isUserConnectedLogin'],
        ]);
    }

    public function forgotPassword(): void
    {
        session_start();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST["email_user"] ?? '';

            try {
                if (empty($email)) {
                    throw new Exception("Veuillez entrer votre adresse e-mail.");
                }

                // 1. Chercher l'utilisateur par email
                $user = $this->entityManager->getRepository(UserAdmin::class)->findOneBy(['email' => $email]);

                if (!$user) {
                    // Pour des raisons de sécurité, ne pas révéler si l'email existe ou non.
                    // On envoie un message générique.
                    $_SESSION['success_message'] = "Si cette adresse e-mail existe, un lien de réinitialisation vous a été envoyé.";
                    $this->redirect("/avva-admin/mot-de-passe-oublie");
                    return;
                }

                // 2. Générer un code temporaire pour la réinitialisation
                $loginUser = new LoginUser($this->entityManager);
                // Utiliser la fonction existante pour générer un code temporaire, même si le but est la réinitialisation
                $loginUser->generateTemporaryCode($user);

                // 3. Afficher le message de succès
                $_SESSION['success_message'] = "Un code de réinitialisation vous a été envoyé par e-mail.";

                // Redirection pour éviter la resoumission du formulaire
                $this->redirect("/avva-admin/mot-de-passe-oublie");

            } catch (Exception $e) {
                $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
                $this->redirect("/avva-admin/mot-de-passe-oublie");
            }
        }

        // Affichage de la vue du formulaire
        $this->render('admin/account/forgot-password');
    }

    private function generateTemporaryCode(): string
    {
        return bin2hex(random_bytes(6));  // Génère un code temporaire de 12 caractères
    }

    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();

        $_SESSION['success_message'] = "Vous êtes maintenant déconnecté.";
        $this->redirect('/avva-admin/login');
    }

    public function listeUsersAdmin(): void
    {
        session_start();

        $active2 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn() || !$_SESSION['user']['idRole'] == 1) {
            header("Location: /avva-admin/login");
            exit;
        }

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $usersAdmin = $this->entityManager->getRepository(UserAdmin::class)->findAll();

        $this->render('/admin/account/liste-utilisateur', [
            'user' => $_SESSION['user'],
            'active2' => $active2,
            'pages' => $pages,
            'usersAdmin' => $usersAdmin
        ]);
    }

    public function creerUtilisateur(): void
    {
        session_start();

        $active2 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn() || !$_SESSION['user']['idRole'] == 1) {
            header("Location: /avva-admin/login");
            exit;
        }

        $user = $_SESSION['user']; // Utilisateur stocké dans la session

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $usersAdmin = $this->entityManager->getRepository(UserAdmin::class)->findAll();

        $nom = '';
        $prenom = '';
        $email = '';
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nom = $_POST["nom_utilisateur"];
            $prenom = $_POST["prenom_utilisateur"];
            $email = $_POST["email_utilisateur"];
            $role = $this->entityManager->getRepository(Role::class)->find($_POST['role_utilisateur']);

            try {
                $creerUtilisateur = new CreerUtilisateur($this->entityManager);
                $utilisateur = $creerUtilisateur->execute($nom, $prenom, $email, $role);

                $this->sendCreateAccountEmail($email, $role->getNom());

                $_SESSION['success_message'] = "Compte créé avec succès !";
                $this->redirect("/avva-admin/liste-utilisateur");
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->render('/admin/account/creer-utilisateur', [
            'user' => $_SESSION['user'],
            'active2' => $active2,
            'pages' => $pages,
            'usersAdmin' => $usersAdmin,
            'roles' => $roles,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
        ]);
    }

    public function modifierUtilisateur(int $id): void
    {
        session_start();

        $active2 = true;

        // Vérifier si l'utilisateur est connecté
        if (!$this->isUserLoggedIn() || !$_SESSION['user']['idRole'] == 1) {
            header("Location: /admin/login");
            exit;
        }

        $user = $_SESSION['user'];

        $pages = $this->entityManager->getRepository(Page::class)->findAll();

        $userAdmin = $this->entityManager->getRepository(UserAdmin::class)->find($id);

        $roles = $this->entityManager->getRepository(Role::class)->findAll();

        if (!$userAdmin) {
            $_SESSION['error_message'] = "Utilisateur introuvable.";
            $this->redirect("/avva-admin/liste-utilisateur");
            exit();
        }

        $modifierUtilisateur = new ModifierUtilisateur($this->entityManager);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nom = $_POST['nom_utilisateur'];
                $prenom = $_POST['prenom_utilisateur'];
                $email = $_POST['email_utilisateur'];
                $role = $this->entityManager->getRepository(Role::class)->find($_POST['role_utilisateur']);

                if (empty($nom) || empty($prenom) || empty($email) || empty($role)) {
                    throw new \Exception("Tous les champs doivent être remplis.");
                }

                $modifierUtilisateur->execute($userAdmin->getId(), $nom, $prenom, $email, $role);

                $_SESSION['success_message'] = "L'utilisateur a été modifiée avec succès";
                $this->redirect("/avva-admin/liste-utilisateur");
                exit();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $this->render('/admin/account/modifier-utilisateur', [
            'user' => $user,
            'active2' => $active2,
            'pages' => $pages,
            'roles' => $roles,
            'error' => $error ?? '',
            'utilisateur' => $_SESSION['utilisateur'] ?? null,
            'userAdmin' => $userAdmin
        ]);
    }

    private function sendCreateAccountEmail(string $email, string $role): void
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
            $mail->Subject = 'Compte utilisateur pour l\'espace membres du bureau de AVVA39';
            $mail->Body = "Un compte a été créé pour accèder à l'espace membres du bureau de AVVA39 sur https://avva39.fr/avva-admin.<br>Une fois connecté, rentré votre email : $email. <br>Ensuite, un mail contenant un code a été envoyé, saisissez dans le champ. <br>Puis, créez un mot de passe. <br>Et enfin, connectez-vous. <br>Vous avez comme rôle : $role.";

            // Envoi de l'email
            $mail->send();
        } catch (Exception $e) {
            // Si l'email n'a pas pu être envoyé
            error_log("L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
            throw new Exception("Erreur lors de l'envoi de l'email.");
        }
    }

    private function isUserLoggedIn(): bool
    {
        session_start();
        return isset($_SESSION['user']);
    }
}