<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'circuits_randonnee')]
class CircuitRandonnee
{
    // =========================================================================
    // 1. CHAMPS D'IDENTIFICATION ET LIEN
    // =========================================================================
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_circuit_randonnee', type: 'integer')]
    private int $id;

    // =========================================================================
    // 2. CHAMPS SPÉCIFIQUES AU CIRCUIT
    // =========================================================================

    #[ORM\Column(name: 'nom_circuit_randonnee', type: 'string', length: 255)]
    private string $nom = 'Circuit principal'; 
    
    // Description du circuit (utile pour la variante)
    #[ORM\Column(name: 'description_circuit_randonnee', type: 'text', nullable: true)]
    private ?string $description = null; 

    #[ORM\Column(name: 'distance_km_circuit', type: 'float')]
    private float $distanceKm;

    #[ORM\Column(name: 'duree_heures_circuit', type: 'float', nullable: true)]
    private ?float $dureeHeures = null;

    #[ORM\Column(name: 'denivele_positif_circuit', type: 'integer')]
    private int $denivelePositif;

    #[ORM\Column(name: 'difficulte_circuit', type: 'string', length: 50)]
    private string $difficulte;
    
    #[ORM\Column(name: 'fichier_gpx_circuit', type: 'string', length: 255, nullable: true)]
    private ?string $fichierGpx = null;

    #[ORM\Column(name: 'est_principal_circuit', type: 'boolean')]
    private bool $estPrincipal = false; // Indique si c'est le circuit par défaut

    #[ORM\Column(name: 'prix_inscription_moins_18_ans_licencie_centimes', type: 'float')]
    private float $prixInscriptionMoins18AnsLicencieCentimes = 0;

    #[ORM\Column(name: 'prix_inscription_moins_18_ans_non_licencie_centimes', type: 'float')]
    private float $prixInscriptionMoins18AnsNonLicencieCentimes = 0;

    #[ORM\Column(name: 'prix_inscription_adulte_licencie_centimes', type: 'float')]
    private float $prixInscriptionAdulteLicencieCentimes = 0;

    #[ORM\Column(name: 'prix_inscription_adulte_non_licencie_centimes', type: 'float')]
    private float $prixInscriptionAdulteNonLicencieCentimes = 0;

    #[ORM\Column(name: 'type_circuit', type: 'string', length: 50)]
    private string $type;

    // RELATION : Clé étrangère vers Randonnee
    #[ORM\ManyToOne(targetEntity: Randonnee::class, inversedBy: 'circuits')]
    #[ORM\JoinColumn(name: 'randonnee_id', referencedColumnName: 'id_randonnee', nullable: false)]
    private Randonnee $randonnee;

    // =========================================================================
    // 3. GETTERS & SETTERS
    // =========================================================================

    public function getId(): int
    {
        return $this->id;
    }
    
    // ... Ajoutez les Getters et Setters pour les autres propriétés (nom, distanceKm, etc.) ...
    public function getNom(): string {
        return $this->nom;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getDistanceKm(): float {
        return $this->distanceKm;
    }

    public function setDistanceKm(float $distanceKm): void {
        $this->distanceKm = $distanceKm;
    }

    public function getDureeHeures(): ?float {
        return $this->dureeHeures;
    }

    public function setDureeHeures(?float $dureeHeures): void {
        $this->dureeHeures = $dureeHeures;
    }

    public function getDenivelePositif(): int {
        return $this->denivelePositif;
    }

    public function setDenivelePositif(int $denivelePositif): void {
        $this->denivelePositif = $denivelePositif;
    }

    public function getDifficulte(): string {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): void {
        $this->difficulte = $difficulte;
    }

    public function getFichierGpx(): ?string {
        return $this->fichierGpx;
    }

    public function setFichierGpx(?string $fichierGpx): void {
        $this->fichierGpx = $fichierGpx;
    }

    public function isEstPrincipal(): bool {
        return $this->estPrincipal;
    }

    public function setEstPrincipal(bool $estPrincipal): void {
        $this->estPrincipal = $estPrincipal;
    }

    public function getPrixInscriptionMoins18AnsLicencieCentimes(): float
    {
        return $this->prixInscriptionMoins18AnsLicencieCentimes;
    }

    public function setPrixInscriptionMoins18AnsLicencieCentimes(float $prixInscriptionMoins18AnsLicencieCentimes): void
    {
        $this->prixInscriptionMoins18AnsLicencieCentimes = $prixInscriptionMoins18AnsLicencieCentimes;
    }

    public function getPrixInscriptionMoins18AnsNonLicencieCentimes(): float
    {
        return $this->prixInscriptionMoins18AnsNonLicencieCentimes;
    }

    public function setPrixInscriptionMoins18AnsNonLicencieCentimes(float $prixInscriptionMoins18AnsNonLicencieCentimes): void
    {
        $this->prixInscriptionMoins18AnsNonLicencieCentimes = $prixInscriptionMoins18AnsNonLicencieCentimes;
    }

    public function getPrixInscriptionAdulteLicencieCentimes(): float
    {
        return $this->prixInscriptionAdulteLicencieCentimes;
    }

    public function setPrixInscriptionAdulteLicencieCentimes(float $prixInscriptionAdulteLicencieCentimes): void
    {
        $this->prixInscriptionAdulteLicencieCentimes = $prixInscriptionAdulteLicencieCentimes;
    }

    public function getPrixInscriptionAdulteNonLicencieCentimes(): float
    {
        return $this->prixInscriptionAdulteNonLicencieCentimes;
    }

    public function setPrixInscriptionAdulteNonLicencieCentimes(float $prixInscriptionAdulteNonLicencieCentimes): void
    {
        $this->prixInscriptionAdulteNonLicencieCentimes = $prixInscriptionAdulteNonLicencieCentimes;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): void {
        $this->type = $type;
    }

    public function getRandonnee(): Randonnee
    {
        return $this->randonnee;
    }

    public function setRandonnee(Randonnee $randonnee): void
    {
        $this->randonnee = $randonnee;
    }
}