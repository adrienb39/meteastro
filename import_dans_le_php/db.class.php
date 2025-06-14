<?php

require_once __DIR__ . "/../vendor/autoload.php";

class Db
{

    public function __construct()
    {
        try {
            $dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
            $dotEnv->load();

            $this->conn = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $e) {
            echo "Connection Failed :" . $e->getMessage();
        }
    }


    public function query($query)
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}