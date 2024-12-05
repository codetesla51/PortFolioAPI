<?php
namespace DB;
use PDO;
use PDOException;

class DB
{
  private string $DB_NAME = "projex";
  private string $DB_PASS = "root";
  private string $DB_USER = "root";
  private string $DB_HOST = "127.0.0.1";
  private ?PDO $connection = null;

  public function connect(): ?PDO
  {
    if ($this->connection === null) {
      try {
        $dsn = "mysql:host={$this->DB_HOST};dbname={$this->DB_NAME}";
        $this->connection = new PDO($dsn, $this->DB_USER, $this->DB_PASS);
        $this->connection->setAttribute(
          PDO::ATTR_ERRMODE,
          PDO::ERRMODE_EXCEPTION
        );
        $this->connection->setAttribute(
          PDO::ATTR_DEFAULT_FETCH_MODE,
          PDO::FETCH_ASSOC
        );
      } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
      }
    }
    return $this->connection;
  }

  public function disconnectDB(): void
  {
    $this->connection = null;
  }
}
