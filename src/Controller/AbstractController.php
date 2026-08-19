<?php

namespace App\Controller;

abstract class AbstractController
{
    protected function render(string $template, array $data = []): void
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../../templates/' . $template . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../templates/base.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    public function renderError(int $code = 404, ?string $message = null, array $data = []): void
    {
        http_response_code($code);

        $defaultMessages = [
            403 => 'Accès refusé. Vous n\'avez pas les autorisations nécessaires.',
            404 => 'Désolé, la page que vous recherchez s\'est évaporée dans le cosmos.',
            500 => 'Une erreur interne est survenue sur le serveur.',
        ];

        $errorMessage = $message ?? ($defaultMessages[$code] ?? 'Une erreur est survenue.');

        // Fusion des paramètres généraux et des données personnalisées ($data)
        $viewParams = array_merge([
            'code' => $code,
            'message' => $errorMessage
        ], $data);

        // Rendu du template
        $this->render('error/404', $viewParams);
        exit;
    }

    protected function renderPartial(string $template, array $data = []): string
    {
        // Rendre le template sans layout
        extract($data); // transforme les clés du tableau en variables
        ob_start();     // démarre la capture du buffer
        include __DIR__ . '/../../templates/' . ltrim($template, '/') . '.php';
        return ob_get_clean(); // retourne le contenu capturé
    }

}