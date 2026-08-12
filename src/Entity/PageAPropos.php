<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'page_a_propos')]
class PageAPropos
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_page_a_propos', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'contenu_page_a_propos', type: "string")]
    private string $contenu;

    public function getId(): int
    {
        return $this->id;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): void
    {
        $this->contenu = $contenu;
    }
}