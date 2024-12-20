<?php

namespace Model;

use DB\DB;
use PDO;
use PDOException;

class User
{
  private PDO $DB;

  public function __construct()
  {
    $this->DB = DB::getInstance()->connect();
  }

  /**
   * Create a new user in the database.
   *
   * @param string $name
   * @param string $email
   * @param string $password
   * @param string $key
   * @return bool
   */
  public function createUser(
    string $name,
    string $email,
    string $password,
    string $key
  ): bool {
    try {
      $query = "INSERT INTO users (user_name, user_password, user_email, user_key) 
                      VALUES (:name, :password, :email, :key)";
      $stmt = $this->DB->prepare($query);
      $stmt->bindParam(":name", $name);
      $stmt->bindParam(":password", $password);
      $stmt->bindParam(":email", $email);
      $stmt->bindParam(":key", $key);
      return $stmt->execute();
    } catch (PDOException $e) {
      // Log error (optional)
      return false;
    }
  }

  /**
   * Check if an email is already taken.
   *
   * @param string $email
   * @return bool
   */
  public function isEmailTaken(string $email): bool
  {
    $query = "SELECT COUNT(*) as count FROM users WHERE user_email = :email";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result["count"] > 0;
  }

  /**
   * Get a user's email by their API key.
   *
   * @param string $apiKey
   * @return string|null
   */
  public function getEmailByApiKey(string $apiKey): ?string
  {
    $query = "SELECT user_email FROM users WHERE user_key = :apiKey";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":apiKey", $apiKey);

    if ($stmt->execute()) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result["user_email"] ?? null;
    }

    return null;
  }

  /**
   * Fetch a user's details by email.
   *
   * @param string $email
   * @return array|null
   */
  public function getUserByEmail(string $email): ?array
  {
    $query = "SELECT * FROM users WHERE user_email = :email LIMIT 1";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":email", $email);

    if ($stmt->execute()) {
      return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    return null;
  }
  public function updateApiKey(int $userId, string $newApiKey): bool
  {
    $query = "UPDATE users SET user_key = :newKey WHERE id = :userId";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":newKey", $newApiKey);
    $stmt->bindParam(":userId", $userId);

    return $stmt->execute();
  }
}
