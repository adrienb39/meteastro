<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'membres')]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: 'id_membre', type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: 'numero_licence_membre', type: "integer", unique: true, nullable: true)]
    private ?int $numeroLicence = null;

    #[ORM\Column(name: 'nom_membre', type: "string", length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(name: 'prenom_membre', type: "string", length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(name: 'date_naissance_membre', type: "date", nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(name: 'sexe_membre', type: "string", length: 1, nullable: true)]
    private ?string $sexe = null;

    #[ORM\Column(name: 'numero_voie_membre', type: "integer", nullable: true)]
    private ?int $numeroVoie = null;

    #[ORM\Column(name: 'nom_voie_membre', type: "string", length: 50, nullable: true)]
    private ?string $nomVoie = null;

    #[ORM\Column(name: 'code_postal_membre', type: "string", length: 5, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(name: 'ville_membre', type: "string", length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: 'numero_telephone_membre', type: "string", length: 50, nullable: true)]
    private ?string $numeroTelephone = null;

    #[ORM\Column(name: 'email_membre', type: "string", length: 50, unique: true)]
    private string $email;

    #[ORM\Column(name: 'plan_membre', type: 'string', length: 20, nullable: true)]
    private ?string $plan = 'trial';

    #[ORM\Column(name: 'code_acces_membre', type: 'string', length: 50, nullable: true)]
    private ?string $codeAcces = null;

    #[ORM\Column(name: 'totp_membre', type: 'string', length: 255, nullable: true)]
    private ?string $totpSecret = null;

    #[ORM\Column(name: 'cle_activation_membre', type: 'string', length: 255, nullable: true)]
    private ?string $cleActivation = null;

    #[ORM\Column(name: 'date_fin_essai_membre', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFinEssai = null;

    #[ORM\Column(name: 'date_creation_membre', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroLicence(): ?int
    {
        return $this->numeroLicence;
    }

    public function setNumeroLicence(?int $numeroLicence): void
    {
        $this->numeroLicence = $numeroLicence;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): void
    {
        $this->sexe = $sexe;
    }

    public function getNumeroVoie(): ?int
    {
        return $this->numeroVoie;
    }

    public function setNumeroVoie(?int $numeroVoie): void
    {
        $this->numeroVoie = $numeroVoie;
    }

    public function getNomVoie(): ?string
    {
        return $this->nomVoie;
    }

    public function setNomVoie(?string $nomVoie): void
    {
        $this->nomVoie = $nomVoie;
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

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(?string $numeroTelephone): void
    {
        $this->numeroTelephone = $numeroTelephone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(?string $plan): void
    {
        $this->plan = $plan;
    }

    public function getCodeAcces(): ?string
    {
        return $this->codeAcces;
    }

    public function setCodeAcces(?string $codeAcces): void
    {
        $this->codeAcces = $codeAcces;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): void
    {
        $this->totpSecret = $totpSecret;
    }

    public function getCleActivation(): ?string
    {
        return $this->cleActivation;
    }

    public function setCleActivation(?string $cleActivation): void
    {
        $this->cleActivation = $cleActivation;
    }

    public function getDateFinEssai(): ?\DateTimeInterface
    {
        return $this->dateFinEssai;
    }

    public function setDateFinEssai(?\DateTimeInterface $dateFinEssai): void
    {
        $this->dateFinEssai = $dateFinEssai;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }
}