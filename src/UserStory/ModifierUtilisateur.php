<?php

namespace App\UserStory;

use App\Entity\Role;
use App\Entity\UserAdmin;
use Doctrine\ORM\EntityManager;

class modifierUtilisateur
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $utilisateurId, string $nom, string $prenom, string $email, Role $role): void
    {
        $userAdmin = $this->entityManager->getRepository(UserAdmin::class)->find($utilisateurId);

        if (!$userAdmin)
            throw new \Exception("Utilisateur introuvable.");

        $userAdmin->setNom($nom);
        $userAdmin->setPrenom($prenom);
        $userAdmin->setEmail($email);
        $userAdmin->setRole($role);

        $this->entityManager->flush();
    }
}