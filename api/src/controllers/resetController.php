<?php
use Helpers\decrypt;
use DB\DB;

class ResetController
{
  private $decrypt;
  private $DB;
  private $table = "admins";
  private $userTable = "users";
  private $logTable = "log";
  private $middleware;

  public function __construct(DB $DB)
  {
    $this->decrypt = new Decrypt();
    $this->DB = DB::getInstance()->connect();
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
      $stmt = $this->DB->prepare($sql);
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

  public function deleteAllRecords(): bool
  {
    $userKey = $this->middleware->handle(true);
    if (!$userKey) {
      http_response_code(401);
      echo json_encode(["message" => "Unauthorized access."]);
      return false;
    }

    try {
      $sql = "DELETE FROM {$this->logTable}";
      $stmt = $this->DB->prepare($sql);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(["message" => "All records deleted successfully."]);
        return true;
      } else {
        http_response_code(204);
        echo json_encode(["message" => "No records to delete."]);
        return false;
      }
    } catch (\PDOException $e) {
      http_response_code(500);
      echo json_encode([
        "message" => "Internal Server Error.",
        "error" => $e->getMessage(),
      ]);
      error_log($e->getMessage());
      return false;
    }
  }
}
