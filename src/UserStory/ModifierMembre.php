<?php

namespace App\UserStory;

use App\Entity\Membre;
use Doctrine\ORM\EntityManager;

class modifierMembre
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $membreId, int $numeroLicence, string $nom, string $prenom, string $dateNaissance, string $sexe, int $numeroVoie, string $nomVoie, string $codePostal, string $ville, string $numeroTelephone, string $email): void
    {
        $membre = $this->entityManager->getRepository(Membre::class)->find($membreId);

        try {
            // Utiliser DateTimeImmutable pour l'immuabilité et la fiabilité
            $dateNaissance = new \DateTime($dateNaissance);
        } catch (\Exception $e) {
            throw new \Exception("Format de date de naissance invalide. Veuillez utiliser le format YYYY-MM-DD.");
        }

        if (!$membre)
            throw new \Exception("Membre introuvable.");

        $membre->setNumeroLicence($numeroLicence);
        $membre->setNom($nom);
        $membre->setPrenom($prenom);
        $membre->setDateNaissance($dateNaissance);
        $membre->setSexe($sexe);
        $membre->setNumeroVoie($numeroVoie);
        $membre->setNomVoie($nomVoie);
        $membre->setCodePostal($codePostal);
        $membre->setVille($ville);
        $membre->setNumeroTelephone($numeroTelephone);
        $membre->setEmail($email);

        $this->entityManager->flush();
    }
}