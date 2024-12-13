<?php

namespace DB;

use PDO;
use PDOException;

class DB
{
  private string $DB_NAME = "freedb_portfolioApi";
  private string $DB_PASS = "FdW#!#ggs&Sx3s!";
  private string $DB_USER = "freedb_uthman";
  private string $DB_HOST = "sql.freedb.tech";
  private ?PDO $connection = null;

  // Connect to the database
  public function connect(): ?PDO
  {
    // Only create a new connection if it does not exist
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
    // Ensure the connection is not already null before attempting to disconnect
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

try {
  $db = new DB();

  $connection = $db->connect();

  if ($connection) {
    echo "Database connected successfully.<br>";
  } else {
    echo "Failed to connect to the database.<br>";
  }

  if ($db->isConnected()) {
  } else {
    echo "Database was not connected.<br>";
  }
} catch (Exception $e) {
  echo "An error occurred: " . $e->getMessage();
}
