<?php

require_once __DIR__ . "/../vendor/autoload.php";

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ ."/../");
$dotEnv->load();

$db = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);