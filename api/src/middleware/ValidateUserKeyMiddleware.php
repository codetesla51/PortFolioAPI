<?php
namespace Middleware;

use Helpers\decrypt;
use DB\DB;

class ApiKeyMiddleware
{
  private $decrypt;
  private $db;
  private $table = "users";
  private int $dailyEmailLimit = 3;
  private int $dailyRequestLimit = 7;
  private ?string $userKey = null;

  public function __construct()
  {
    $this->decrypt = new Decrypt();
    $this->db = (new DB())->connect();
  }

  /**
   * Main handler for API key validation and request limits.
   */
  public function handle(bool $isAdmin = false): ?string
  {
    $this->table = $isAdmin ? "admins" : "users";
    $headers = getallheaders();
    $this->userKey = $headers["API-Key"] ?? null;

    if (!$this->userKey) {
      $this->sendResponse(401, "API key required");
    }

    // Validate and fetch the encrypted key
    $encryptedKey = $this->getEncryptedKeyByDecryptedKey();

    if (!$encryptedKey) {
      $this->sendResponse($isAdmin ? 401 : 403, "Invalid API key");
    }

    // Return the user key if everything is valid
    return $this->userKey;
  }

  /**
   * Validate the API key against the database.
   */
  private function isValidUserKey(): bool
  {
    $keys = $this->fetchAllKeys();

    foreach ($keys as $encryptedKey) {
      try {
        if ($this->decrypt->DecryptKey($encryptedKey) === $this->userKey) {
          return true;
        }
      } catch (\Exception $e) {
        error_log("Decryption error: " . $e->getMessage());
      }
    }

    return false;
  }

  /**
   * Fetch all user keys from the database.
   */
  private function fetchAllKeys(): array
  {
    try {
      $query = "SELECT user_key FROM {$this->table} WHERE user_key IS NOT NULL";
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    } catch (\PDOException $e) {
      error_log("Database query failed: " . $e->getMessage());
      $this->sendResponse(500, "Internal server error");
    }

    return [];
  }

  /**
   * Fetch user details by API key.
   */
  private function getUserByApiKey(): ?array
  {
    $keys = $this->fetchAllKeys();

    foreach ($keys as $encryptedKey) {
      try {
        if ($this->decrypt->DecryptKey($encryptedKey) === $this->userKey) {
          $query = "SELECT * FROM {$this->table} WHERE user_key = :key";
          $stmt = $this->db->prepare($query);
          $stmt->bindParam(":key", $encryptedKey, \PDO::PARAM_STR);
          $stmt->execute();
          return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
      } catch (\Exception $e) {
        error_log("Decryption error: " . $e->getMessage());
      }
    }

    return null;
  }

  /**
   * Check if the user is within the daily email limit.
   */
  public function isUnderDailyEmailLimit(): bool
  {
    $user = $this->getUserByApiKey();
    if (!$user) {
      $this->sendResponse(403, "Invalid API key");
    }

    return ($user["emailsentToday"] ?? 0) < $this->dailyEmailLimit;
  }

  /**
   * Increment the daily email count for the user.
   */
  public function incrementEmailCount(): void
  {
    $this->incrementField("emailsentToday");
  }

  /**
   * Check if the user is within the daily request limit.
   */
  public function isUnderDailyRequestLimit(): bool
  {
    $user = $this->getUserByApiKey();
    if (!$user) {
      $this->sendResponse(403, "Invalid API key");
    }

    return ($user["normalRequestToday"] ?? 0) < $this->dailyRequestLimit;
  }

  /**
   * Increment the daily request count for the user.
   */
  public function incrementRequestCount(): void
  {
    $this->incrementField("normalRequestToday");
  }

  /**
   * Increment a specific field in the database for the user.
   */
  private function incrementField(string $field): void
  {
    try {
      // Fetch the encrypted key for the given user key
      $encryptedKey = $this->getEncryptedKeyByDecryptedKey();

      if (!$encryptedKey) {
        $this->sendResponse(403, "Invalid API key");
      }

      // Update the field in the database
      $query = "UPDATE {$this->table} SET {$field} = {$field} + 1 WHERE user_key = :key";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(":key", $encryptedKey, \PDO::PARAM_STR);

      if (!$stmt->execute()) {
        $this->sendResponse(500, "Failed to update {$field}");
      }
    } catch (\PDOException $e) {
      error_log("Database update failed: " . $e->getMessage());
      $this->sendResponse(500, "Internal server error");
    }
  }

  /**
   * Get encrypted key by decrypted key.
   */
  private function getEncryptedKeyByDecryptedKey(): ?string
  {
    $keys = $this->fetchAllKeys();

    foreach ($keys as $encryptedKey) {
      try {
        // Check if the decrypted key matches the provided user key
        if ($this->decrypt->DecryptKey($encryptedKey) === $this->userKey) {
          return $encryptedKey; // Return the valid encrypted key
        }
      } catch (\Exception $e) {
        error_log("Decryption error: " . $e->getMessage());
      }
    }

    // If no valid key is found, return null
    return null;
  }

  /**
   * Send an HTTP response with a status code and message.
   */
  private function sendResponse(int $statusCode, string $message): void
  {
    http_response_code($statusCode);
    echo json_encode(["error" => $message]);
    exit();
  }
}
