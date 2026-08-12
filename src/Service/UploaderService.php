<?php

namespace App\Service;

use Exception; // Utiliser l'exception de base ou une customisée

class UploaderService
{
    /**
     * @var string Le chemin physique complet du répertoire de destination (ex: /var/www/public/uploads/medias/)
     */
    private string $targetDirectory;

    /**
     * Liste des extensions de fichiers autorisées.
     * @var array
     */
    private const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif', // Images
        'mp4',
        'mov',
        'avi',
        'webm', // Vidéos
        'pdf',
        'PDF'
    ];

    /**
     * Constructeur
     *
     * @param string $targetDirectory Le chemin physique complet du répertoire de destination.
     */
    public function __construct(string $targetDirectory)
    {
        // S'assurer que le chemin se termine par un slash
        $this->targetDirectory = rtrim($targetDirectory, '/') . '/';
    }

    /**
     * Gère l'upload d'un fichier.
     *
     * @param array $fileData Le tableau $_FILES['nom_du_champ']
     * @return string Le chemin relatif public du fichier uploadé (ex: 'uploads/medias/nom_unique.jpg')
     * @throws Exception Si l'upload échoue ou si le fichier est invalide.
     */
    public function upload(array $fileData): string
    {
        // 1. Validation de la taille et de l'erreur initiale
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur d'upload du fichier. Code: " . $fileData['error']);
        }

        // 2. Validation de l'extension
        $fileName = $fileData['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, self::ALLOWED_EXTENSIONS)) {
            throw new Exception("Extension de fichier non autorisée : ." . $fileExtension);
        }

        // 3. Renommage unique du fichier
        // Crée un nom de fichier unique pour éviter les écrasements
        $newFileName = uniqid() . '.' . $fileExtension;

        // 4. Déplacement du fichier
        $targetPath = $this->targetDirectory . $newFileName;

        // Vérifie si le dossier de destination existe, sinon le crée
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0777, true)) {
            throw new Exception("Impossible de créer le répertoire de destination : " . $this->targetDirectory);
        }

        if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            throw new Exception("Échec du déplacement du fichier uploadé.");
        }

        // 5. Retourne le chemin relatif public
        // Cette étape est cruciale pour enregistrer le chemin en BDD. 
        // L'utilisateur du service (le contrôleur) doit savoir quelle partie du chemin est publique.
        // On suppose que le répertoire public est 'uploads/medias/'
        $publicPath = 'uploads/medias/';
        return $publicPath . $newFileName;
    }

    /**
     * Gère l'upload d'un fichier.
     *
     * @param array $fileData Le tableau $_FILES['nom_du_champ']
     * @return string Le chemin relatif public du fichier uploadé (ex: 'uploads/medias/nom_unique.jpg')
     * @throws Exception Si l'upload échoue ou si le fichier est invalide.
     */
    public function uploadPdf(array $fileData): string
    {
        // 1. Validation de la taille et de l'erreur initiale
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur d'upload du fichier. Code: " . $fileData['error']);
        }

        // 2. Validation de l'extension
        $originalName = $fileData['name'];
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, self::ALLOWED_EXTENSIONS)) {
            throw new Exception("Extension de fichier non autorisée : ." . $fileExtension);
        }

        // 3. Nettoyage du nom et renommage unique
        // On récupère le nom sans l'extension
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        // On remplace les espaces par des underscores
        $cleanName = str_replace(' ', '_', $baseName);

        // On ajoute un ID unique pour la sécurité, tout en gardant le nom lisible
        $newFileName = $cleanName . '_' . uniqid() . '.' . $fileExtension;

        // 4. Déplacement du fichier
        $targetPath = $this->targetDirectory . $newFileName;

        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0777, true)) {
            throw new Exception("Impossible de créer le répertoire de destination.");
        }

        if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            throw new Exception("Échec du déplacement du fichier.");
        }

        // 5. Retourne le chemin relatif public
        $publicPath = 'uploads/page-comment-adherer/pdf/';
        return $publicPath . $newFileName;
    }
}