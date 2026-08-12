<?php

namespace App\UserStory;

use App\Entity\Membre;
use Doctrine\ORM\EntityManager;

class CreerMembre
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $numeroLicence, string $nom, string $prenom, string $dateNaissance, string $sexe, int $numeroVoie, string $nomVoie, string $codePostal, string $ville, string $numeroTelephone, string $email, string $cleActivation): Membre {
        if (empty($numeroLicence) || empty($nom) || empty($prenom) || empty($dateNaissance) || empty($sexe) || empty($numeroVoie) || empty($nomVoie) || empty($codePostal) || empty($ville) || empty($numeroTelephone) || empty($email)) {
            throw new \Exception("Tous les champs sont obligatoires");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email invalide");
        }

        $existEmail = $this->entityManager->getRepository(Membre::class)->findOneBy(['email' => $email]);
        if ($existEmail) {
            throw new \Exception("Email déjà existant");
        }

        try {
            // Utiliser DateTimeImmutable pour l'immuabilité et la fiabilité
            $dateNaissance = new \DateTime($dateNaissance);
        } catch (\Exception $e) {
            throw new \Exception("Format de date de naissance invalide. Veuillez utiliser le format YYYY-MM-DD.");
        }

        $membre = new Membre();
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
        $membre->setCleActivation($cleActivation);

        $this->entityManager->persist($membre);
        $this->entityManager->flush();

        return $membre;
    }
}