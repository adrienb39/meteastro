<?php

use Doctrine\ORM\EntityManager;

// Définir le chemin absolu vers le dossier 'ffct-challenge'
define('ROOT_PATH', realpath(__DIR__ . '/..'));

// Inclure l'autoloader de Composer
require_once ROOT_PATH . '/vendor/autoload.php';

// Inclure les fichiers de configuration
require_once ROOT_PATH . '/config/bootstrap.php';

// Récupérer les routes
$routes = require_once ROOT_PATH . '/config/routes.php';

// Récupérer l'URL actuelle
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Vérifier si l'URL correspond à une route définie
$routeFound = false;

foreach ($routes as $routePattern => $controllerAction) {
    // Convertir les routes dynamiques (avec {id}, {url}) en expressions régulières
    // Exemple: '/qrcodes/edit/{id}' -> '/qrcodes/edit/(\d+)' pour capturer l'ID
    // Exemple: '/signup/{url}' -> '/signup/([a-zA-Z0-9-]+)' pour capturer l'URL
    $regex = "#^" . str_replace(
        ['{id}', '{idSection}', '{idContenuSection}', '{titre}', '{url}', '{qrCodeUrl}', '{validationId}', '{token}', '{numeroInscription}', '{page}', '{action}', '{slugRandonnee}'], 
        ['(\d+)', '(\d+)', '(\d+)', '([a-zA-Z0-9-]+)', '([a-zA-Z0-9-]+)', '([a-zA-Z0-9-]+)', '(\d+)', '([a-zA-Z0-9\-]+)', '([_a-zA-Z0-9\-]+)', '([a-zA-Z0-9\-]+)', '([a-zA-Z0-9\-]+)', '([a-zA-Z0-9\-]+)'], 
        $routePattern
    ) . "$#";

    // Vérifier si l'URL actuelle correspond à la route
    if (preg_match($regex, $uri, $matches)) {
        // Route correspondante trouvée, on récupère le contrôleur et l'action
        list($controllerName, $action) = $controllerAction;

        // Récupérer le paramètre capturé
        // Le premier paramètre est l'ID ou l'URL capturé
        $params = array_slice($matches, 1);

        // Instancier le contrôleur et appeler l'action
        $controllerClass = "App\\Controller\\{$controllerName}";
        $controller = new $controllerClass($entityManager);

        // Passer le paramètre (id ou url) à l'action
        call_user_func_array([$controller, $action], $params);

        $routeFound = true;
        break;  // Sortir de la boucle dès qu'une route correspondante est trouvée
    }
}

// Si aucune route ne correspond, afficher une erreur 404
if (!$routeFound) {
    $errorController = new \App\Controller\ErrorController($entityManager);
    $errorController->error404();
}