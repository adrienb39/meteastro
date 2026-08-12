<?php

namespace App\UserStory;

use App\Entity\Role;
use App\Entity\UserAdmin;
use Doctrine\ORM\EntityManager;

class CreerUtilisateur
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(string $nom, string $prenom, string $email, Role $role): UserAdmin {
        if (empty($nom) || empty($prenom) || empty($email)) {
            throw new \Exception("Tous les champs sont obligatoires");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email invalide");
        }

        $existEmail = $this->entityManager->getRepository(UserAdmin::class)->findOneBy(['email' => $email]);
        if ($existEmail) {
            throw new \Exception("Email déjà existant");
        }

        $userAdmin = new UserAdmin();
        $userAdmin->setNom($nom);
        $userAdmin->setPrenom($prenom);
        $userAdmin->setEmail($email);
        $userAdmin->setAdmin(0);
        $userAdmin->setRole($role);

        $this->entityManager->persist($userAdmin);
        $this->entityManager->flush();

        return $userAdmin;
    }
}