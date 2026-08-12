<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'defilement_texte')]
class DefilementTexte
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id', type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: 'defilement_texte', type: "string", nullable: true)]
    private ?string $defilementTexte = '';

    #[ORM\Column(name: 'couleur_defilement_texte', type: "string", nullable: true)]
    private ?string $couleurDefilementTexte = '';

    #[ORM\Column(name: 'fond_defilement_texte', type: "string", nullable: true)]
    private ?string $fondDefilementTexte = '';

    #[ORM\Column(name: 'taille_defilement_texte', type: "string", nullable: true)]
    private ?string $tailleDefilementTexte = '';

    #[ORM\Column(name: 'position_defilement_texte', type: "string", nullable: true)]
    private ?string $positionDefilementTexte = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefilementTexte(): ?string
    {
        return $this->defilementTexte;
    }

    public function setDefilementTexte(?string $defilementTexte): void
    {
        $this->defilementTexte = $defilementTexte;
    }

    public function getCouleurDefilementTexte(): ?string
    {
        return $this->couleurDefilementTexte;
    }

    public function setCouleurDefilementTexte(?string $couleurDefilementTexte): void
    {
        $this->couleurDefilementTexte = $couleurDefilementTexte;
    }

    public function getFondDefilementTexte(): ?string
    {
        return $this->fondDefilementTexte;
    }

    public function setFondDefilementTexte(?string $fondDefilementTexte): void
    {
        $this->fondDefilementTexte = $fondDefilementTexte;
    }

    public function getTailleDefilementTexte(): ?string
    {
        return $this->tailleDefilementTexte;
    }

    public function setTailleDefilementTexte(?string $tailleDefilementTexte): void
    {
        $this->tailleDefilementTexte = $tailleDefilementTexte;
    }

    public function getPositionDefilementTexte(): ?string
    {
        return $this->positionDefilementTexte;
    }

    public function setPositionDefilementTexte(?string $positionDefilementTexte): void
    {
        $this->positionDefilementTexte = $positionDefilementTexte;
    }
}