<?php

require_once __DIR__ . "/../vendor/autoload.php";

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ ."/../");
$dotEnv->load();

/**
 * Function to create a PDO connection
 * 
 * @return PDO
 * @throws Exception
 */
function createPdoConnection() {
    try {
        $db = new PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (Exception $e) {
        die("Could not connect to the database: " . $e->getMessage());
    }
}

/**
 * Function to create a MySQLi connection
 * 
 * @return mysqli
 * @throws Exception
 */
function createMysqliConnection() {
    $mysqli = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    return $mysqli;
}