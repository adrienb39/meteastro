<?php
session_start();

// Vérifier que l'utilisateur est authentifié
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_password'])) {
    header('Location: connexion/login.php');  // Rediriger si non authentifié
    exit;
}

// Vérifier que le token est fourni et qu'il correspond à celui de la session
if (isset($_GET['token']) && $_GET['token'] === $_SESSION['download_token']) {
    // Le token est valide, autoriser le téléchargement
    $file = $_GET['file'];
    $file_path = 'download/media/' . basename($file);  // Assurez-vous que le fichier existe et est sécurisé

    // Vérifiez que le fichier existe réellement
    if (file_exists($file_path)) {
        // Forcer le téléchargement du fichier
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file_path);
        exit;
    } else {
        echo "Fichier non trouvé.";
        exit;
    }
} else {
    // Token invalide, accès refusé
    header('HTTP/1.0 403 Forbidden');
    echo "Accès interdit - Token invalide.";
    exit;
}