<?php

namespace App\Controller;

class NewsletterCronController
{
    public const LATEST_VERSION = '2.5.6';

    /**
     * Renvoie la version de l'application au format JSON.
     */
    public function checkNewsletter(): void
    {
        // Nettoie tout flux déjà émis (évite les erreurs de headers)
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'status'  => 'success',
            'version' => self::LATEST_VERSION,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}