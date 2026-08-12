<?php

namespace App\UserStory;

use App\Entity\CategorieEvent;
use App\Entity\DispositionPageAccueil;
use App\Entity\Page;
use App\Entity\PageDate;
use Doctrine\ORM\EntityManager;

class ModifierPage
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $pageId, string $nom, string $url, string $contenu, DispositionPageAccueil $dispositionPageAccueil, string $imageGauche, string $imageDroite): void
    {
        $page = $this->entityManager->getRepository(Page::class)->find($pageId);

        if (!$page)
            throw new \Exception("Page introuvable.");

        $page->setNom($nom);
        $page->setUrl($url);
        $page->setContenu($contenu);
        $page->setDispositionPageAccueil($dispositionPageAccueil);
        $page->setImageGauche($imageGauche);
        $page->setImageDroite($imageDroite);

        $this->entityManager->flush();
    }
}