<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'types_sorties')]
class TypeSortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_type_sortie', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'nom_type_sortie', type: "string", length: 50)]
    private string $nom;

    /**
     * Relation inverse : Permet d'accéder aux sorties depuis un type.
     * mappedBy doit correspondre au nom de la propriété dans l'entité Sortie.
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'typesSorties')]
    private Collection $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function setSortie(Sortie $sortie): void
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties->add($sortie);
            // On informe l'autre côté de la relation (important pour la cohérence)
            $sortie->setTypeSortie($this);
        }
    }

    /**
     * Permet d'afficher le nom du type directement dans un template ou un formulaire
     */
    public function __toString(): string
    {
        return $this->nom;
    }
}