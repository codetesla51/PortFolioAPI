<?php

namespace DB;

use PDO;
use PDOException;
use Exception;
use Dotenv\Dotenv;

class DB
{
  private string $DB_NAME;
  private string $DB_PASS;
  private string $DB_USER;
  private string $DB_HOST;
  private ?PDO $connection = null;

  public function __construct()
  {

    $this->DB_NAME =
      $_ENV["DB_NAME"] ?? throw new Exception("DB_NAME not set in .env");
    $this->DB_PASS =
      $_ENV["DB_PASSWORD"] ?? throw new Exception("DB_PASS not set in .env");
    $this->DB_USER =
      $_ENV["DB_USER"] ?? throw new Exception("DB_USER not set in .env");
    $this->DB_HOST =
      $_ENV["DB_HOST"] ?? throw new Exception("DB_HOST not set in .env");
  }

  // Connect to the database
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
        throw new Exception("Database connection failed: " . $e->getMessage());
      }
    }
    return $this->connection;
  }

  // Disconnect from the database
  public function disconnectDB(): void
  {
    if ($this->connection !== null) {
      $this->connection = null; // Close the connection
    }
  }

  // Check if connected to the database
  public function isConnected(): bool
  {
    return $this->connection !== null;
  }
}

// Usage Example
try {
  $db = new DB();
  $connection = $db->connect();

  if ($connection) {
    echo "Database connected successfully.<br>";
  } else {
    echo "Failed to connect to the database.<br>";
  }

  if ($db->isConnected()) {
    echo "Database is still connected.<br>";
  } else {
    echo "Database was disconnected.<br>";
  }

  // Disconnect
  $db->disconnectDB();
} catch (Exception $e) {
  echo "An error occurred: " . $e->getMessage();
}
