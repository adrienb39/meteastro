<?php

namespace App\Entity;

use App\Repository\SortieRepository; // Optionnel : ajoutez votre repository si existant
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sorties_hebdomadaires')]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_sortie', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'titre_sortie', type: "string", length: 50)]
    private string $titre;

    #[ORM\Column(name: 'description_sortie', type: "string", length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_sortie', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $date;

    #[ORM\Column(name: 'temps_sortie', type: Types::TIME_MUTABLE)]
    private \DateTimeInterface $temps;

    #[ORM\Column(name: 'difficulte_sortie', type: "string", length: 50, nullable: true)]
    private ?string $difficulte;

    /**
     * Correction : Passage en ManyToMany pour permettre la sélection multiple.
     * Une table de jointure sera automatiquement créée.
     */
    #[ORM\ManyToMany(targetEntity: TypeSortie::class)]
    #[ORM\JoinTable(name: 'sortie_types_relation')]
    #[ORM\JoinColumn(name: 'sortie_id', referencedColumnName: 'id_sortie')]
    #[ORM\InverseJoinColumn(name: 'type_sortie_id', referencedColumnName: 'id_type_sortie')]
    private Collection $typesSorties;

    public function __construct()
    {
        $this->typesSorties = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getTemps(): \DateTimeInterface
    {
        return $this->temps;
    }

    public function setTemps(\DateTimeInterface $temps): void
    {
        $this->temps = $temps;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(?string $difficulte): void
    {
        $this->difficulte = $difficulte;
    }

    /**
     * @return Collection<int, TypeSortie>
     */
    public function getTypesSorties(): Collection
    {
        return $this->typesSorties;
    }

    public function setTypeSortie(TypeSortie $typeSortie): void
    {
        if (!$this->typesSorties->contains($typeSortie)) {
            $this->typesSorties->add($typeSortie);
        }
    }
}