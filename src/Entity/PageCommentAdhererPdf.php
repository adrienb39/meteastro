<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'page_comment_adherer_pdf')]
class PageCommentAdhererPdf
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_page_comment_adherer_pdf', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'thematique_page_comment_adherer_pdf', type: "string", length: 50)]
    private string $thematique;

    #[ORM\Column(name: 'nom_page_comment_adherer_pdf', type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(name: 'description_page_comment_adherer_pdf', type: "string")]
    private string $description;

    #[ORM\Column(name: 'fichier_page_comment_adherer_pdf', type: "string", length: 255)]
    private string $fichier;

    #[ORM\Column(name: 'est_afficher', type: "boolean")]
    private bool $estAfficher;

    #[ORM\Column(name: 'est_telechargeable', type: "boolean")]
    private bool $estTelechargeable;

    // #[ORM\ManyToOne(targetEntity: DispositionPageAccueil::class)]
    // #[ORM\JoinColumn(name: 'disposition_page_accueil_id', referencedColumnName: 'id_disposition_page_accueil')]
    // private DispositionPageAccueil $dispositionPageAccueil;

    public function getId(): int
    {
        return $this->id;
    }

    public function getThematique(): string
    {
        return $this->thematique;
    }

    public function setThematique(string $thematique): void
    {
        $this->thematique = $thematique;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getFichier(): string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): void
    {
        $this->fichier = $fichier;
    }

    public function getEstAfficher(): bool
    {
        return $this->estAfficher;
    }

    public function setEstAfficher(bool $estAfficher): void
    {
        $this->estAfficher = $estAfficher;
    }

    public function getEstTelechargeable(): bool
    {
        return $this->estTelechargeable;
    }

    public function setEstTelechargeable(bool $estTelechargeable): void
    {
        $this->estTelechargeable = $estTelechargeable;
    }

    // public function getDispositionPageAccueil(): DispositionPageAccueil
    // {
    //     return $this->dispositionPageAccueil;
    // }

    // public function setDispositionPageAccueil(DispositionPageAccueil $dispositionPageAccueil): void
    // {
    //     $this->dispositionPageAccueil = $dispositionPageAccueil;
    // }
}