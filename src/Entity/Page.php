<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pages')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_page', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'nom_page', type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(name: 'url_page', type: "string", length: 255)]
    private string $url;

    #[ORM\Column(name: 'contenu_page', type: "string")]
    private string $contenu;

    #[ORM\Column(name: 'ordre_page_accueil', type: "integer")]
    private int $ordrePageAccueil;

    #[ORM\ManyToOne(targetEntity: DispositionPageAccueil::class)]
    #[ORM\JoinColumn(name: 'disposition_page_accueil_id', referencedColumnName: 'id_disposition_page_accueil')]
    private DispositionPageAccueil $dispositionPageAccueil;

    #[ORM\Column(name: 'image_gauche_titre', type: "string", length: 255, nullable: true)]
    private ?string $imageGauche;

    #[ORM\Column(name: 'image_droite_titre', type: "string", length: 255, nullable: true)]
    private ?string $imageDroite;

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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): void
    {
        $this->contenu = $contenu;
    }

    public function getOrdrePageAccueil(): int
    {
        return $this->ordrePageAccueil;
    }

    public function setOrdrePageAccueil(int $ordrePageAccueil): void
    {
        $this->ordrePageAccueil = $ordrePageAccueil;
    }

    public function getDispositionPageAccueil(): DispositionPageAccueil
    {
        return $this->dispositionPageAccueil;
    }

    public function setDispositionPageAccueil(DispositionPageAccueil $dispositionPageAccueil): void
    {
        $this->dispositionPageAccueil = $dispositionPageAccueil;
    }

    public function getImageGauche(): ?string
    {
        return $this->imageGauche;
    }

    public function setImageGauche(?string $imageGauche): void
    {
        $this->imageGauche = $imageGauche;
    }

    public function getImageDroite(): ?string
    {
        return $this->imageDroite;
    }

    public function setImageDroite(?string $imageDroite): void
    {
        $this->imageDroite = $imageDroite;
    }
}