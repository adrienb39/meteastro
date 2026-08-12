<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'inscriptions_pedestre_randonnee')]
class InscriptionPedestreRandonnee
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    // --- Informations du Participant ---

    #[ORM\Column(name: 'nom', type: 'string', length: 50)]
    private string $nom;

    #[ORM\Column(name: 'prenom', type: 'string', length: 50)]
    private string $prenom;

    #[ORM\Column(name: 'sexe', type: 'string', length: 1)]
    private string $sexe;

    #[ORM\Column(name: 'date_naissance', type: 'date')]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(name: 'num_tel', type: 'string', length: 50)]
    private string $numTel;

    #[ORM\Column(name: 'email', type: 'string', length: 50)]
    private string $email;

    // --- Informations d'Adresse et d'Urgence (Uniques au participant principal/payeur) ---

    // Rendu nullable pour les participants secondaires, comme le contrôleur envoie 'null'.
    #[ORM\Column(name: 'adresse', type: 'string', length: 50, nullable: true)]
    private ?string $adresse = null;

    // Rendu nullable pour les participants secondaires.
    #[ORM\Column(name: 'code_postal', type: 'string', length: 5, nullable: true)]
    private ?string $codePostal = null;

    // Rendu nullable pour les participants secondaires.
    #[ORM\Column(name: 'ville', type: 'string', length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: 'nom_prenom_tel', type: 'text')]
    private string $nomPrenomTel;

    // --- Informations de Licence (Optionnel) ---

    #[ORM\Column(name: 'licence_ffvelo_club', type: 'string', length: 200, nullable: true)]
    private ?string $licenceFfveloClub = null;

    #[ORM\Column(name: 'num_licence', type: 'string', length: 50, nullable: true)]
    private ?string $numLicence = null;

    #[ORM\Column(name: 'autre_federation_club', type: 'string', length: 200, nullable: true)]
    private ?string $autreFederationClub = null;

    // --- Liens et Statut de Paiement ---

    #[ORM\ManyToOne(targetEntity: CircuitRandonnee::class)]
    #[ORM\JoinColumn(name: 'circuit_randonnee_id', referencedColumnName: 'id_circuit_randonnee')]
    private CircuitRandonnee $circuitRandonnee;

    #[ORM\Column(name: 'statut_paiement', type: 'string', length: 20)]
    private string $statutPaiement = 'ATTENTE_PAIEMENT';

    // Le champ `a_payer` est remplacé par `statut_paiement` pour plus de granularité.
    // L'ancien `aPayer` peut être déduit du montant ou du statut.
    // Si l'on conserve 'aPayer' pour des raisons de rétro-compatibilité:
    /*
    #[ORM\Column(name: 'a_payer', type: Types::BOOLEAN)]
    private bool $aPayer = false;
    */

    #[ORM\Column(name: 'numero_inscription', type: 'string', length: 200)]
    private string $numeroInscription; // Identifiant de groupe pour tous les participants liés au même paiement.

    // --- Getters et Setters ---

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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): void
    {
        $this->sexe = $sexe;
    }

    public function getDateNaissance(): \DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): void
    {
        $this->ville = $ville;
    }

    public function getNumTel(): string
    {
        return $this->numTel;
    }

    public function setNumTel(string $numTel): void
    {
        $this->numTel = $numTel;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getNomPrenomTel(): string
    {
        return $this->nomPrenomTel;
    }

    public function setNomPrenomTel(string $nomPrenomTel): void
    {
        $this->nomPrenomTel = $nomPrenomTel;
    }

    public function getLicenceFfveloClub(): ?string
    {
        return $this->licenceFfveloClub;
    }

    public function setLicenceFfveloClub(?string $licenceFfveloClub): void
    {
        $this->licenceFfveloClub = $licenceFfveloClub;
    }

    public function getNumLicence(): ?string
    {
        return $this->numLicence;
    }

    public function setNumLicence(?string $numLicence): void
    {
        $this->numLicence = $numLicence;
    }

    public function getAutreFederationClub(): ?string
    {
        return $this->autreFederationClub;
    }

    public function setAutreFederationClub(?string $autreFederationClub): void
    {
        $this->autreFederationClub = $autreFederationClub;
    }

    public function getCircuitRandonnee(): CircuitRandonnee
    {
        return $this->circuitRandonnee;
    }

    public function setCircuitRandonnee(CircuitRandonnee $circuitRandonnee): void
    {
        $this->circuitRandonnee = $circuitRandonnee;
    }

    public function getStatutPaiement(): string
    {
        return $this->statutPaiement;
    }

    public function setStatutPaiement(string $statutPaiement): void
    {
        $this->statutPaiement = $statutPaiement;
    }

    /* Si on conserve l'ancien champ:
    public function getAPayer(): bool {
        return $this->aPayer;
    }

    public function setAPayer(bool $aPayer): self {
        $this->aPayer = $aPayer;
        return $this;
    }
    */

    public function getNumeroInscription(): string
    {
        return $this->numeroInscription;
    }

    public function setNumeroInscription(string $numeroInscription): void
    {
        $this->numeroInscription = $numeroInscription;
    }
}