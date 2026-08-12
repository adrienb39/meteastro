<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'gpx_parcours')]
class GpxParcours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Membre::class)]
    #[ORM\JoinColumn(name: 'id_membre', referencedColumnName: 'id_membre', nullable: false, onDelete: 'CASCADE')]
    private ?Membre $membre = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 150)]
    private string $nom;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'fichier_url', type: 'string')]
    private string $fichierUrl;

    #[ORM\Column(name: 'est_publique', type: 'boolean')]
    private bool $estPublique;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private \DateTime $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime', nullable: true)]
    private ?\DateTime $dateModification;

    // Getters et Setters...
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }
    public function setMembre(?Membre $membre): void
    {
        $this->membre = $membre;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getFichierUrl(): string
    {
        return $this->fichierUrl;
    }
    public function setFichierUrl(string $fichierUrl): void
    {
        $this->fichierUrl = $fichierUrl;
    }

    public function getEstPublique(): bool
    {
        return $this->estPublique;
    }
    public function setEstPublique(bool $estPublique): void
    {
        $this->estPublique = $estPublique;
    }

    public function getDateCreation(): \DateTime
    {
        return $this->dateCreation;
    }
    public function setDateCreation(\DateTime $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }
    public function setDateModification(?\DateTime $dateModification): void
    {
        $this->dateModification = $dateModification;
    }
}