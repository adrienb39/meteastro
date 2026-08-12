<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// Ce fichier permet de configurer la console afin de la lier à l'entityManager
// Ainsi, toutes les commandes pourront "communiquer avec la base de données" via l'entityManager

// Récupérer l'EntityManager
/**
 * @var Doctrine\ORM\EntityManager $entityManager
 */
$entityManager = require_once __DIR__.'/../config/bootstrap.php';

ConsoleRunner::run(new SingleManagerProvider($entityManager));