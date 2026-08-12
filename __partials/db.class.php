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

    /**
     * Exécute une requête SQL
     * @param string $query La requête SQL (avec des ? ou :params)
     * @param array $params Les données à injecter
     */
    public function query2($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        // Si la requête commence par SELECT ou SHOW, on retourne les résultats
        if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i', $query)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Sinon (UPDATE, INSERT, DELETE), on retourne le statement ou le nombre de lignes
        return $stmt;
    }
}