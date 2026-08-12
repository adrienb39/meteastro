<?php

namespace App\UserStory;

use App\Entity\DispositionPageAccueil;
use App\Entity\Page;
use App\Entity\Section;
use Doctrine\ORM\EntityManager;

class CreerPage
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(string $nom, string $url, string $contenu, int $ordrePageAccueil, DispositionPageAccueil $dispositionPageAccueil): Page {
        if (empty($nom) || empty($url) || empty($contenu) || empty($ordrePageAccueil) || empty($dispositionPageAccueil)) {
            throw new \Exception("Tous les champs sont obligatoires");
        }

        if (strlen($nom) > 50) {
            throw new \Exception("Le nom de la section ne doit pas dépassé 50 caractères.");
        }

        if (strlen($url) > 255) {
            throw new \Exception("L'url de la section ne doit pas dépassé 255 caractères.");
        }

        $page = new Page();
        $page->setNom($nom);
        $page->setUrl($url);
        $page->setContenu($contenu);
        $page->setOrdrePageAccueil($ordrePageAccueil);
        $page->setDispositionPageAccueil($dispositionPageAccueil);

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page;
    }
}