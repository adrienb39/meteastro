<?php

use App\Command\SendNewsletterCommand;
use App\Service\NewsletterMailerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @var EntityManager $entityManager
 */
$entityManager = require_once __DIR__ . '/../config/bootstrap.php';

// 1. Initialisation des services nécessaires
$projectDir = dirname(__DIR__);
$newsletterService = new NewsletterMailerService($entityManager, $projectDir);

// 2. Création de l'application Console avec le provider Doctrine
$commands = [
    new SendNewsletterCommand($newsletterService),
];

$cli = ConsoleRunner::createApplication(
    new SingleManagerProvider($entityManager),
    $commands
);

// 3. Exécution de la console
$cli->run();