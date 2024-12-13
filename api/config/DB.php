<?php

namespace DB;

use PDO;
use PDOException;

class DB
{
  private string $DB_NAME = "4443499_projex";
  private string $DB_PASS = "+11Sl-(e7_wt[j@)";
  private string $DB_USER = "4443499_projex";
  private string $DB_HOST = "fdb1032.awardspace.net";
  private ?PDO $connection = null;

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
        die("Database connection failed: " . $e->getMessage());
      }
    }
    return $this->connection;
  }

  // Disconnect from the database
  public function disconnectDB(): void
  {
    $this->connection = null;
  }
}

// Testing the DB class
try {
  // Create an instance of the DB class
  $db = new DB();

  // Try to connect to the database
  $connection = $db->connect();

  if ($connection) {
    echo "Database connected successfully.<br>";
  } else {
    echo "Failed to connect to the database.<br>";
  }

  // Disconnect from the database
  $db->disconnectDB();
  echo "Database disconnected.";
} catch (Exception $e) {
  echo "An error occurred: " . $e->getMessage();
}
