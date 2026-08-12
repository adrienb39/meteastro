<?php

namespace App\UserStory;

use App\Entity\CategorieEvent;
use App\Entity\DateEvent;
use App\Entity\DecompteDepartSortie;
use Doctrine\ORM\EntityManager;

class ModifierDecompteDepartSortie
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $pageId, string $titre, string $description, \DateTime $date): void {
        // Récupérer l'entité challenge actuelle
        $page = $this->entityManager->getRepository(DecompteDepartSortie::class)->find($pageId);

        if (!$page) {
            throw new \Exception("Page introuvable.");
        }

        $categorie = $this->entityManager->getRepository(CategorieEvent::class)->find(4);

        $dateEvent = new DateEvent();
        $dateEvent->setTitre($titre);
        $dateEvent->setDescription($description);
        $dateEvent->setCategorieEvent($categorie);
        $dateEvent->setDateStart($date);
        $dateEvent->setDateEnd($date);

        // Mettre à jour l'entité challenge avec les nouvelles valeurs
        $page->setTitre($titre);
        $page->setDescription($description);
        $page->setDate($date);

        $this->entityManager->persist($dateEvent);

        // Enregistrer les modifications dans la base de données
        $this->entityManager->flush();
    }
}