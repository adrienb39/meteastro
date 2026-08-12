<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\UserAdmin; // Utilisation de l'entité UserAdmin
use Doctrine\ORM\EntityManager;

class ProfileController extends AbstractController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Charge et affiche le formulaire de modification du profil de l'utilisateur connecté.
     * URL: /avva-admin/profile
     */
    public function profile(): void
    {
        // 1. Démarrer la session et vérifier l'authentification
        session_start();

        if (!$this->isUserLoggedIn()) {
            $this->redirect('/avva-admin/login');
            return;
        }

        $active15 = true;

        // 2. Récupérer l'ID de l'utilisateur stocké en session
        // Note: Assurez-vous que l'ID est stocké sous la clé 'user_id' lors de la connexion.
        $userId = $_SESSION['user']['id'] ?? null;

        $pages = $this->entityManager->getRepository(Page::class)->findAll();
        
        // 3. Charger l'entité utilisateur (UserAdmin)
        $userEntity = $this->entityManager->getRepository(UserAdmin::class)->find($userId);

        if (!$userEntity) {
            // Gérer le cas où l'utilisateur n'est plus trouvé en base
            $this->redirect('/avva-admin/logout');
            return;
        }

        // Afficher la vue du profil avec l'entité utilisateur
        $this->render('admin/profile', [
            'user' => $_SESSION['user'],
            'active15' => $active15,
            'pages' => $pages,
            'userEntity' => $userEntity, // Passe l'entité UserAdmin complète à la vue
            'active_profile' => true
        ]);
    }

    /**
     * Traite la soumission du formulaire de profil (modification du nom, prénom, email et mot de passe).
     * URL: /avva-admin/profile/save (méthode POST)
     */
    public function save(): void
    {
        // 1. Démarrer la session et vérifier l'authentification
        session_start();

        if (!$this->isUserLoggedIn()) {
            $this->redirect('/avva-admin/login');
            return;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $userEntity = $this->entityManager->getRepository(UserAdmin::class)->find($userId);

        if (!$userEntity) {
            $_SESSION['error_message'] = "Erreur: Utilisateur non trouvé.";
            $this->redirect('/avva-admin/profile');
            return;
        }

        // 2. Récupération et nettoyage des données POST (on suppose que le formulaire envoie 'nom' et 'prenom')
        $data = [
            'nom' => filter_input(INPUT_POST, 'nom'),
            'prenom' => filter_input(INPUT_POST, 'prenom'),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'current_password' => filter_input(INPUT_POST, 'current_password'),
            'new_password' => filter_input(INPUT_POST, 'new_password'),
            'new_password_confirm' => filter_input(INPUT_POST, 'new_password_confirm'),
        ];
        
        // 3. Validation
        if (empty($data['nom']) || empty($data['prenom']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Le **nom**, le **prénom** et l'**email** sont obligatoires et doivent être valides.";
            $this->redirect('/avva-admin/profile');
            return;
        }

        try {
            // 4. Mise à jour des informations de base (Nom, Prénom et Email)
            $userEntity->setNom($data['nom']);
            $userEntity->setPrenom($data['prenom']);
            $userEntity->setEmail($data['email']);

            // 5. Gestion du changement de mot de passe
            if (!empty($data['new_password'])) {
                // Vérifier l'ancien mot de passe
                if (!password_verify($data['current_password'], $userEntity->getPassword())) {
                    $_SESSION['error_message'] = "Le mot de passe actuel est incorrect.";
                    $this->redirect('/avva-admin/profile');
                    return;
                }

                // Vérifier la confirmation du nouveau mot de passe
                if ($data['new_password'] !== $data['new_password_confirm']) {
                    $_SESSION['error_message'] = "Les nouveaux mots de passe ne correspondent pas.";
                    $this->redirect('/avva-admin/profile');
                    return;
                }

                // Mettre à jour et hacher le nouveau mot de passe
                $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
                $userEntity->setPassword($hashedPassword);
            }
            
            // 6. Sauvegarde
            $this->entityManager->persist($userEntity);
            $this->entityManager->flush();

            $_SESSION['success_message'] = "Votre profil a été mis à jour avec succès.";

        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur : Impossible d'enregistrer le profil. " . $e->getMessage();
        }

        $this->redirect('/avva-admin/profile');
    }

    private function isUserLoggedIn(): bool
    {
        session_start();
        return isset($_SESSION['user']);
    }
}