<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'randonnees')]
class Randonnee
{
    // =========================================================================
    // 1. CHAMPS D'IDENTIFICATION ET DE BASE
    // =========================================================================
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_randonnee', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'titre_randonnee', type: 'string', length: 255)]
    private string $titre; 

    #[ORM\Column(name: 'slug_randonnee', type: 'string', length: 255, unique: true)]
    private ?string $slug = null; 

    #[ORM\Column(name: 'date_creation_randonnee', type: 'datetime')]
    private \DateTime $dateCreation; 

    #[ORM\Column(name: 'date_mise_a_jour_randonnee', type: 'datetime')]
    private \DateTime $dateMiseAJour; 

    // =========================================================================
    // 2. CHAMPS DE CONTENU ET DE LIEU (GÉNÉRAUX)
    // =========================================================================

    #[ORM\Column(name: 'description_courte_randonnee', type: 'text')]
    private string $descriptionCourte;

    #[ORM\Column(name: 'description_complete_randonnee', type: 'text')]
    private string $descriptionComplete;

    #[ORM\Column(name: 'lieu_depart_randonnee', type: 'string', length: 255)]
    private string $lieuDepart;

    #[ORM\Column(name: 'coordonnees_gps_randonnee', type: 'string', length: 100, nullable: true)]
    private ?string $coordonneesGps = null;

    // ATTENTION : Les champs techniques (distance, dénivelé, etc.) ont été déplacés dans CircuitRandonnee.

    // =========================================================================
    // 3. CHAMPS D'AFFICHAGE ET MÉDIAS
    // =========================================================================

    #[ORM\Column(name: 'image_principale_randonnee', type: 'string', length: 255, nullable: true)]
    private ?string $imagePrincipale = null;

    #[ORM\Column(name: 'galerie_photos_randonnee', type: 'json', nullable: true)]
    private ?array $galeriePhotos = [];

    #[ORM\Column(name: 'couleur_thematique_randonnee', type: 'string', length: 7)]
    private string $couleurThematique = '#4CAF50';

    #[ORM\Column(name: 'afficher_carte_randonnee', type: 'boolean', nullable: true)]
    private ?bool $afficherCarte = true;

    #[ORM\Column(name: 'modele_page_randonnee', type: 'string', length: 50)]
    private string $modelePage = 'tpl_defaut';

    // =========================================================================
    // 4. CHAMPS DE PLANIFICATION (ÉVÉNEMENT)
    // =========================================================================

    #[ORM\Column(name: 'randonnee_date_randonnee', type: 'datetime')]
    private \DateTime $dateRandonnee;

    #[ORM\Column(name: 'nombre_participants_max_randonnee', type: 'integer', nullable: true)]
    private ?int $nombreParticipantsMax = 0;

    #[ORM\Column(name: 'statut_inscription_randonnee', type: 'string', length: 50)]
    private string $statutInscription = 'Ouvert';

    #[ORM\Column(name: 'est_annulee_randonnee', type: 'boolean')]
    private bool $estAnnulee = false;

    #[ORM\Column(name: 'message_annulation_randonnee', type: 'text', nullable: true)]
    private ?string $messageAnnulation = null;

    // =========================================================================
    // 5. CHAMPS D'ADMINISTRATION ET SEO
    // =========================================================================

    #[ORM\Column(name: 'statut_publication_randonnee', type: 'string', length: 50)]
    private string $statutPublication = 'Brouillon';

    #[ORM\Column(name: 'notes_internes_randonnee', type: 'text', nullable: true)]
    private ?string $notesInternes = null;

    // =========================================================================
    // 0. RELATION : CIRCUITS (One-to-Many)
    // =========================================================================
    
    /**
     * @var Collection<int, CircuitRandonnee>
     */
    #[ORM\OneToMany(mappedBy: 'randonnee', targetEntity: CircuitRandonnee::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $circuits;


    // =========================================================================
    // 6. CONSTRUCTEUR, GETTERS & SETTERS
    // =========================================================================

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->dateMiseAJour = new \DateTime();
        $this->circuits = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    // --- Base ---

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
    
    public function getDateCreation(): \DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function getDateMiseAJour(): \DateTime
    {
        return $this->dateMiseAJour;
    }

    public function setDateMiseAJour(\DateTime $dateMiseAJour): void
    {
        $this->dateMiseAJour = $dateMiseAJour;
    }

    // --- Contenu & Lieu ---

    public function getDescriptionCourte(): string
    {
        return $this->descriptionCourte;
    }

    public function setDescriptionCourte(?string $descriptionCourte): void
    {
        $this->descriptionCourte = $descriptionCourte;
    }
    
    public function getDescriptionComplete(): string
    {
        return $this->descriptionComplete;
    }

    public function setDescriptionComplete(string $descriptionComplete): void
    {
        $this->descriptionComplete = $descriptionComplete;
    }

    public function getLieuDepart(): string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): void
    {
        $this->lieuDepart = $lieuDepart;
    }

    public function getCoordonneesGps(): ?string
    {
        return $this->coordonneesGps;
    }

    public function setCoordonneesGps(?string $coordonneesGps): void
    {
        $this->coordonneesGps = $coordonneesGps;
    }

    // --- Affichage & Médias ---

    public function getImagePrincipale(): ?string
    {
        return $this->imagePrincipale;
    }

    public function setImagePrincipale(?string $imagePrincipale): void
    {
        $this->imagePrincipale = $imagePrincipale;
    }

    public function getGaleriePhotos(): ?array
    {
        return $this->galeriePhotos;
    }

    public function setGaleriePhotos(?array $galeriePhotos): void
    {
        $this->galeriePhotos = $galeriePhotos;
    }

    public function getCouleurThematique(): string
    {
        return $this->couleurThematique;
    }

    public function setCouleurThematique(string $couleurThematique): void
    {
        $this->couleurThematique = $couleurThematique;
    }
    
    public function isAfficherCarte(): ?bool
    {
        return $this->afficherCarte;
    }

    public function setAfficherCarte(?bool $afficherCarte): void
    {
        $this->afficherCarte = $afficherCarte;
    }

    public function getModelePage(): string
    {
        return $this->modelePage;
    }

    public function setModelePage(string $modelePage): void
    {
        $this->modelePage = $modelePage;
    }

    // --- Planification ---

    public function getDateRandonnee(): \DateTimeInterface
    {
        return $this->dateRandonnee;
    }

    public function setDateRandonnee(\DateTimeInterface $dateRandonnee): void
    {
        $this->dateRandonnee = $dateRandonnee;
    }

    public function getNombreParticipantsMax(): ?int
    {
        return $this->nombreParticipantsMax;
    }

    public function setNombreParticipantsMax(?int $nombreParticipantsMax): void
    {
        $this->nombreParticipantsMax = $nombreParticipantsMax;
    }
    
    public function getStatutInscription(): string
    {
        return $this->statutInscription;
    }

    public function setStatutInscription(string $statutInscription): void
    {
        $this->statutInscription = $statutInscription;
    }
    
    public function isEstAnnulee(): bool
    {
        return $this->estAnnulee;
    }

    public function setEstAnnulee(bool $estAnnulee): void
    {
        $this->estAnnulee = $estAnnulee;
    }

    public function getMessageAnnulation(): ?string
    {
        return $this->messageAnnulation;
    }

    public function setMessageAnnulation(?string $messageAnnulation): void
    {
        $this->messageAnnulation = $messageAnnulation;
    }

    // --- Administration & SEO ---

    public function getStatutPublication(): string
    {
        return $this->statutPublication;
    }

    public function setStatutPublication(string $statutPublication): void
    {
        $this->statutPublication = $statutPublication;
    }
    
    public function getNotesInternes(): ?string
    {
        return $this->notesInternes;
    }

    public function setNotesInternes(?string $notesInternes): void
    {
        $this->notesInternes = $notesInternes;
    }

    // --- Relation Circuits (Méthodes de Collection) ---

    /**
     * @return Collection<int, CircuitRandonnee>
     */
    public function getCircuits(): Collection
    {
        return $this->circuits;
    }

    public function addCircuit(CircuitRandonnee $circuit): self
    {
        if (!$this->circuits->contains($circuit)) {
            $this->circuits[] = $circuit;
            $circuit->setRandonnee($this);
        }
        return $this;
    }

    public function removeCircuit(CircuitRandonnee $circuit): self
    {
        if ($this->circuits->removeElement($circuit)) {
            // set the owning side to null (unless already changed)
            if ($circuit->getRandonnee() === $this) {
                $circuit->setRandonnee(null);
            }
        }
        return $this;
    }
}