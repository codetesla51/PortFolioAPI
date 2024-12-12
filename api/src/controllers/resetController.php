<?php
use Helpers\decrypt;
use DB\DB;

class ResetController
{
  private $decrypt;
  private $db;
  private $table = "admins";
  private $userTable = "users";
  private $middleware;

  public function __construct()
  {
    $this->decrypt = new Decrypt();
    $this->db = (new DB())->connect();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function resetUserLimits(): bool
  {
    $userKey = $this->middleware->handle(true);

    if (!$userKey) {
      http_response_code(401);
      echo json_encode(["message" => "Unauthorized access."]);
      return false;
    }

    try {
      $sql = "UPDATE {$this->userTable} SET normalRequestToday = 0, emailsentToday = 0";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
          "message" => "User limits have been successfully reset.",
        ]);
        return true;
      } else {
        http_response_code(204);
        echo json_encode(["message" => "No user limits were updated."]);
        return false;
      }
    } catch (\PDOException $e) {
      http_response_code(500);
      echo json_encode([
        "message" => "An error occurred while resetting user limits.",
        "error" => $e->getMessage(),
      ]);
      return false;
    }
  }
}
