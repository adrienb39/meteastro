<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'gpx_reprises')]
class GpxReprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * Nom personnalisé ou généré de la reprise (ex: "Reprise de : Circuit du lac")
     */
    #[ORM\Column(name: 'nom_reprise', type: Types::STRING, length: 255)]
    private string $nomReprise;

    /**
     * Le parcours original d'où provient la reprise
     */
    #[ORM\ManyToOne(targetEntity: GpxParcours::class)]
    #[ORM\JoinColumn(name: 'id_parcours_source', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private GpxParcours $parcoursSource;

    /**
     * Le nouveau parcours dupliqué créé par le membre
     */
    #[ORM\ManyToOne(targetEntity: GpxParcours::class)]
    #[ORM\JoinColumn(name: 'id_parcours_copie', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private GpxParcours $parcoursCopie;

    /**
     * Le membre ayant effectué la reprise
     */
    #[ORM\ManyToOne(targetEntity: Membre::class)]
    #[ORM\JoinColumn(name: 'id_membre', referencedColumnName: 'id_membre', onDelete: 'CASCADE')]
    private Membre $membre;

    /**
     * Date et heure de la reprise (Fuseau horaire Europe/Paris)
     */
    #[ORM\Column(name: 'date_reprise', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $dateReprise;

    /**
     * Constructeur
     */
    public function __construct(
        GpxParcours $source, 
        GpxParcours $copie, 
        Membre $membre, 
        string $nomReprise
    ) {
        $this->parcoursSource = $source;
        $this->parcoursCopie = $copie;
        $this->membre = $membre;
        $this->nomReprise = $nomReprise;
        $this->dateReprise = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    }

    // =========================================================================
    // GETTERS & SETTERS
    // =========================================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomReprise(): string
    {
        return $this->nomReprise;
    }

    public function setNomReprise(string $nomReprise): void
    {
        $this->nomReprise = $nomReprise;
    }

    public function getParcoursSource(): GpxParcours
    {
        return $this->parcoursSource;
    }

    public function setParcoursSource(GpxParcours $parcoursSource): void
    {
        $this->parcoursSource = $parcoursSource;
    }

    public function getParcoursCopie(): GpxParcours
    {
        return $this->parcoursCopie;
    }

    public function setParcoursCopie(GpxParcours $parcoursCopie): void
    {
        $this->parcoursCopie = $parcoursCopie;
    }

    public function getMembre(): Membre
    {
        return $this->membre;
    }

    public function setMembre(Membre $membre): void
    {
        $this->membre = $membre;
    }

    public function getDateReprise(): \DateTimeInterface
    {
        return $this->dateReprise;
    }

    public function setDateReprise(\DateTimeInterface $dateReprise): void
    {
        $this->dateReprise = $dateReprise;
    }
}