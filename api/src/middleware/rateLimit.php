<?php
namespace Middleware;
use DB\DB;

class RateLimit
{
  private $maxLimit;
  private $db;
  private $middleware;

  public function __construct()
  {
    $this->db = (new DB())->connect();
    $this->maxLimit = 5;
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function trackRequest(): bool
  {
    $apiKey = $this->middleware->handle();
    if (!$apiKey) {
      throw new \Exception("Invalid API key");
    }

    $currentDate = date("Y-m-d");

    // Check if the key already exists for today
    $query = "SELECT id, request_count FROM api_rate_limit 
                  WHERE user_key = :key AND date = :date";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":key", $apiKey);
    $stmt->bindParam(":date", $currentDate);
    $stmt->execute();
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($result) {
      if ($result["request_count"] >= $this->maxLimit) {
        throw new \Exception("Rate limit exceeded");
      }

      $updateQuery = "UPDATE api_rate_limit 
                            SET request_count = request_count + 1 
                            WHERE id = :id";
      $updateStmt = $this->db->prepare($updateQuery);
      $updateStmt->bindParam(":id", $result["id"]);
      return $updateStmt->execute();
    } else {
      // If the key doesn't exist for today, insert a new record
      $insertQuery = "INSERT INTO api_rate_limit (user_key, date, request_count) 
                            VALUES (:key, :date, 1)";
      $insertStmt = $this->db->prepare($insertQuery);
      $insertStmt->bindParam(":key", $apiKey);
      $insertStmt->bindParam(":date", $currentDate);
      return $insertStmt->execute();
    }
  }
}
