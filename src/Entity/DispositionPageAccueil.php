<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'dispositions_pages_accueil')]
class DispositionPageAccueil
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_disposition_page_accueil', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'nom_disposition_page_accueil', type: "string", length: 50)]
    private string $nom;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
}