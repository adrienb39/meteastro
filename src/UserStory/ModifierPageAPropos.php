<?php

namespace App\UserStory;

use App\Entity\PageAPropos;
use Doctrine\ORM\EntityManager;

class ModifierPageAPropos
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $pageId, string $contenu): void {
        // Récupérer l'entité challenge actuelle
        $page = $this->entityManager->getRepository(PageAPropos::class)->find($pageId);

        if (!$page) {
            throw new \Exception("Page introuvable.");
        }

        // Mettre à jour l'entité challenge avec les nouvelles valeurs
        $page->setContenu($contenu);

        // Enregistrer les modifications dans la base de données
        $this->entityManager->flush();
    }
}