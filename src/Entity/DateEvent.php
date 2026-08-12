<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'date_event')]
class DateEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_date', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'titre_date_event', type: "string", length: 50)]
    private string $titre;

    #[ORM\Column(name: 'description_date_event', type: "string")]
    private string $description;

    #[ORM\Column(name: 'date_start_event', type: 'datetime')]
    private \DateTimeInterface $dateStart;

    #[ORM\Column(name: 'date_end_event', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateEnd;

    #[ORM\ManyToOne(targetEntity: CategorieEvent::class)]
    #[ORM\JoinColumn(name: 'categorie_event_id', referencedColumnName: 'id_categorie_event')]
    private CategorieEvent $categorieEvent;

    #[ORM\Column(name: 'compte_rendu_date_event', type: 'string', nullable: true)]
    private ?string $compteRendu;

    #[ORM\Column(name: 'fichier_gpx_date_event', type: 'string', length: 255, nullable: true)]
    private ?string $gpxFilePath = null;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDateStart(): \DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    public function getCategorieEvent(): CategorieEvent
    {
        return $this->categorieEvent;
    }

    public function setCategorieEvent(CategorieEvent $categorieEvent): void
    {
        $this->categorieEvent = $categorieEvent;
    }

    public function getCompteRendu(): ?string
    {
        return $this->compteRendu;
    }

    public function setCompteRendu(?string $compteRendu): void
    {
        $this->compteRendu = $compteRendu;
    }

    public function getGpxFilePath(): ?string
    {
        return $this->gpxFilePath;
    }

    public function setGpxFilePath(?string $gpxFilePath): void
    {
        $this->gpxFilePath = $gpxFilePath;
    }
}