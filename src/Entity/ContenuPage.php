<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'contenus_pages')]
class ContenuPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_contenu_page', type: "integer")]
    private int $id;

    #[ORM\Column(name: 'nom_contenu_page', type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(name: 'texte_contenu_page', type: "string", nullable: true)]
    private ?string $texte;

    #[ORM\Column(name: 'image_contenu_page', type: "string", length: 255, nullable: true)]
    private ?string $image;

    #[ORM\Column(name: 'video_contenu_page', type: "string", length: 255, nullable: true)]
    private ?string $video;

    #[ORM\Column(name: 'pdf_contenu_page', type: "string", length: 255, nullable: true)]
    private ?string $pdf;

    #[ORM\Column(name: 'ordre_contenu_page', type: "integer")]
    private int $ordre;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id_page')]
    private Page $page;

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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): void
    {
        $this->texte = $texte;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): void
    {
        $this->video = $video;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): void
    {
        $this->pdf = $pdf;
    }

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): void
    {
        $this->ordre = $ordre;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function setPage(Page $page): void
    {
        $this->page = $page;
    }
}