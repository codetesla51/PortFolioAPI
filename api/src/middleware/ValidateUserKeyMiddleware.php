<?php
namespace Middleware;

use Helpers\decrypt;
use DB\DB;

class ApiKeyMiddleware
{
  private $decrypt;
  private $DB;

  public function __construct()
  {
    $this->decrypt = new Decrypt();
    $this->DB = (new DB())->connect();
  }

  public function handle()
  {
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      exit();
    }

    // Verify API key
    if (!$this->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      exit();
    }

    return $userKey;
  }

  private function isValidUserKey(string $userKey): bool
  {
    try {
      $query = "SELECT user_key FROM users";
      $stmt = $this->DB->query($query);
      $keys = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    } catch (\PDOException $e) {
      error_log("Database query failed: " . $e->getMessage());
      return false;
    }

    foreach ($keys as $encryptedKey) {
      try {
        $decryptedKey = $this->decrypt->DecryptKey($encryptedKey);
        if ($decryptedKey === $userKey) {
          return true;
        }
      } catch (\Exception $e) {
        error_log("Decryption error for Error: " . $e->getMessage());
      }
    }

    return false;
  }
}
