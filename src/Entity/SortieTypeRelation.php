<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sortie_types_relation')]
class SortieTypeRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Sortie::class, inversedBy: 'typeRelations')]
    #[ORM\JoinColumn(name: 'sortie_id', referencedColumnName: 'id_sortie')]
    private Sortie $sortie;

    #[ORM\ManyToOne(targetEntity: TypeSortie::class)]
    #[ORM\JoinColumn(name: 'type_sortie_id', referencedColumnName: 'id_type_sortie')]
    private TypeSortie $typeSortie;

    // Optionnel : vous pouvez ajouter des champs ici
    // #[ORM\Column(type: 'datetime')]
    // private \DateTime $dateAjout;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSortie(): Sortie
    {
        return $this->sortie;
    }

    public function setSortie(Sortie $sortie): void
    {
        $this->sortie = $sortie;
    }

    public function getTypeSortie(): TypeSortie
    {
        return $this->typeSortie;
    }

    public function setTypeSortie(TypeSortie $typeSortie): void
    {
        $this->typeSortie = $typeSortie;
    }
}