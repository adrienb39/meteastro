<?php

require_once __DIR__ . "/../vendor/autoload.php";

$paths = [__DIR__ . "/../src/Entity/"];
$isDevMode = true;

$configuration = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// Chargement des variables d'environnement
$dotEnv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotEnv->load(); // Charger les variables d'environnement de .env dans $_ENV
$configurationDB = ['driver' => 'pdo_mysql', "host" => "{$_ENV['DB_HOST']}", "dbname" => "{$_ENV['DB_NAME']}", "user" => "{$_ENV['DB_USER']}", "password" => "{$_ENV['DB_PASSWORD']}"];

$connexionDB = \Doctrine\DBAL\DriverManager::getConnection($configurationDB, $configuration);

$entityManager = new \Doctrine\ORM\EntityManager($connexionDB, $configuration);
return $entityManager;