<?php

namespace App\UserStory;

use App\Entity\CircuitRandonnee; // Utilisé dans l'entité finale
use App\Entity\InscriptionPedestreRandonnee; // Nom de l'entité refactorisée
use Doctrine\ORM\EntityManagerInterface; // Utilisation de l'interface pour le type hinting

class CreerInscriptionPedestreRandonnee
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        // L'entityManager est injecté par dépendance
        $this->entityManager = $entityManager;
    }

    /**
     * Exécute la création et l'enregistrement d'une inscription à une randonnée route.
     * Les champs d'adresse sont facultatifs (nullables) pour les participants secondaires.
     *
     * @param string $nom
     * @param string $prenom
     * @param string $sexe
     * @param \DateTimeInterface $dateNaissance
     * @param ?string $adresse L'adresse est nullable pour les membres secondaires.
     * @param ?string $codePostal Le code postal est nullable pour les membres secondaires.
     * @param ?string $ville La ville est nullable pour les membres secondaires.
     * @param string $numTel
     * @param string $email
     * @param string $nomPrenomTel
     * @param ?string $licenceFfveloClub
     * @param ?string $numLicence
     * @param ?string $autreFederationClub
     * @param CircuitRandonnee $circuitRandonnee Le circuit spécifique choisi.
     * @param string $numeroInscription L'ID de groupe pour lier les inscriptions.
     * @param float $montantPaye Le montant unitaire à payer pour ce participant.
     * @param string $statutPaiement Le statut initial (e.g., 'PAYE' si montant=0, 'ATTENTE_PAIEMENT' sinon).
     * * @return InscriptionPedestreRandonnee
     */
    public function execute(
        string $nom,
        string $prenom,
        string $sexe,
        \DateTimeInterface $dateNaissance, // Meilleur type pour le standard
        ?string $adresse, // Rendu nullable
        ?string $codePostal, // Rendu nullable
        ?string $ville, // Rendu nullable
        string $numTel,
        string $email,
        string $nomPrenomTel,
        ?string $licenceFfveloClub,
        ?string $numLicence,
        ?string $autreFederationClub,
        string $numeroInscription,
        CircuitRandonnee $circuitRandonnee,
        float $montantPaye, // Ajout du montant
        string $statutPaiement // Ajout du statut
    ): InscriptionPedestreRandonnee {

        // --- 1. Validation de la présence des données obligatoires ---
        // Les champs adresse, codePostal et ville ne sont plus vérifiés ici car ils sont optionnels pour certains
        if (empty($nom) || empty($prenom) || empty($sexe) || empty($dateNaissance) || empty($numTel) || empty($email) || empty($nomPrenomTel) || empty($circuitRandonnee)) {
            throw new \InvalidArgumentException("Les informations de base (nom, prénom, contact, circuit) sont obligatoires.");
        }

        // --- 2. Validation du format et des contraintes de longueur ---

        // Vérifier si l'email est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email invalide.");
        }

        // Utilisation d'une fonction d'aide pour la validation de longueur
        $this->validateLength($nom, 50, 'nom');
        $this->validateLength($prenom, 50, 'prénom');
        $this->validateLength($adresse, 50, 'adresse', true);
        $this->validateLength($codePostal, 5, 'code postal', true);
        $this->validateLength($ville, 50, 'ville', true);
        $this->validateLength($numTel, 50, 'numéro de téléphone');
        $this->validateLength($email, 50, 'email');
        $this->validateLength($licenceFfveloClub, 200, 'licence FFVELO et club', true);
        $this->validateLength($numLicence, 50, 'numéro de licence', true);
        $this->validateLength($autreFederationClub, 200, 'autre fédération et club', true);

        // --- 3. Création et Hydratation de l'Entité ---

        // Créer une instance de la nouvelle entité
        $inscription = new InscriptionPedestreRandonnee();

        // Définition des propriétés
        $inscription->setNom($nom);
        $inscription->setPrenom($prenom);
        $inscription->setSexe($sexe);
        $inscription->setDateNaissance($dateNaissance);

        // Ces setters acceptent null
        $inscription->setAdresse($adresse);
        $inscription->setCodePostal($codePostal);
        $inscription->setVille($ville);

        $inscription->setNumTel($numTel);
        $inscription->setEmail($email);
        $inscription->setNomPrenomTel($nomPrenomTel);

        // Les setters acceptent null
        $inscription->setLicenceFfveloClub($licenceFfveloClub);
        $inscription->setNumLicence($numLicence);
        $inscription->setAutreFederationClub($autreFederationClub);

        // Liens et statut
        $inscription->setCircuitRandonnee($circuitRandonnee); // Changement de ParcoursRoute à CircuitRandonnee
        $inscription->setNumeroInscription($numeroInscription);
        $inscription->setStatutPaiement($statutPaiement); // Nouveau champ : statut de paiement (ATTENTE_PAIEMENT/PAYE)

        // Si vous utilisez encore le champ 'aPayer' (ancienne entité):
        // $inscription->setAPayer($montantPaye > 0);

        // --- 4. Enregistrement en Base de Données ---

        $this->entityManager->persist($inscription);
        $this->entityManager->flush();

        return $inscription;
    }

    /**
     * Fonction d'aide pour valider la longueur des chaînes.
     */
    private function validateLength(?string $value, int $maxLength, string $fieldName, bool $nullable = false): void
    {
        if (($nullable && $value === null) || $value === '') {
            // OK si nullable et vide ou null
            return;
        }

        // Si non-nullable et vide/null, la validation initiale aurait dû le détecter, mais on s'assure ici
        if ($value !== null && strlen($value) > $maxLength) {
            throw new \InvalidArgumentException("Le champ '{$fieldName}' ne doit pas dépasser {$maxLength} caractères.");
        }
    }
}