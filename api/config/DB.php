<?php
namespace DB;

use PDO;
use PDOException;
use Exception;

class DB
{
    private string $DB_NAME;
    private string $DB_PASS;
    private string $DB_USER;
    private string $DB_HOST;
    private ?PDO $connection = null;

    public function __construct()
    {
        $this->DB_USER = $_ENV["DB_USER"];
        $this->DB_HOST = $_ENV["DB_HOST"];
        $this->DB_PASS = $_ENV["DB_PASSWORD"];
        $this->DB_NAME = $_ENV["DB_NAME"];
    }

    // Connect to the database
    public function connect(): ?PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->DB_HOST};dbname={$this->DB_NAME}";
                $this->connection = new PDO($dsn, $this->DB_USER, $this->DB_PASS);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return $this->connection;
    }

    public function disconnectDB(): void
    {
        if ($this->connection !== null) {
            $this->connection = null;
        }
    }

    public function isConnected(): bool
    {
        return $this->connection !== null;
    }

}