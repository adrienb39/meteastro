<?php
// Vérifier si l'utilisateur est connecté avant de servir le fichier
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_password'])) {
    // Rediriger l'utilisateur vers la page de connexion ou afficher un message d'erreur
    header('Location: /connexion');  // Remplacer par l'URL de votre page de connexion
    exit;
} else {
    // Si l'utilisateur est connecté, procéder au téléchargement
    $filePath = '/path/to/software/file.zip';  // Le chemin vers le fichier à télécharger

    // Vérifier que le fichier existe
    if (file_exists($filePath)) {
        // Envoyer les en-têtes appropriés pour forcer le téléchargement
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo 'Fichier introuvable.';
    }
}
