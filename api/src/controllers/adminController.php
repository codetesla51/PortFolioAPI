<?php

use Model\Admin;
use Helpers\encrypt;
use Helpers\decrypt;
use DB\DB;
class AdminController
{
  private  $DB;
  private $adminModel;
  private $encrypt;
  private $decrypt;
  private $middleware;
  public function __construct()
  {
    $this->DB = DB::getInstance()->connect();
    $this->adminModel = new Admin();
    $this->encrypt = new Encrypt();
    $this->decrypt = new Decrypt();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function validate(): array
  {
    $fixedUsername = $_ENV["ADMIN_USERNAME"] ?? null;
    $fixedApiKey = $_ENV["ADMIN_API_KEY"] ?? null;
    $encryptedAPIkey = $this->encrypt->EncryptKey($fixedApiKey);
    if (!$fixedUsername || !$fixedApiKey) {
      return [
        "status" => "error",
        "message" => "Admin Not Set",
      ];
    }

    $existingAdmin = $this->adminModel->getAdminByUsername($fixedUsername);

    if ($existingAdmin) {
      return ["status" => "error", "message" => "Admin already exists"];
    }

    $inserted = $this->adminModel->createAdmin(
      $fixedUsername,
      $encryptedAPIkey
    );
    $decryptedAPIkey = $this->decrypt->DecryptKey($encryptedAPIkey);
    if ($inserted) {
      return [
        "status" => "success",
        "message" => "Admin registered successfully",
        "api_key" => $decryptedAPIkey,
      ];
    } else {
      return ["status" => "error", "message" => "Failed to register Admin"];
    }
  }
  public function Count()
  {
    $userKey = $this->middleware->handle(true);
    if (!$userKey) {
      http_response_code(401);
      echo json_encode(["message" => "Unauthorized access."]);
      return false;
    }

    try {
      $queries = $this->adminModel->CountData();

      $results = [];

      foreach ($queries as $key => $query) {
        $stmt = $this->adminModel->DB->prepare($query);
        $stmt->execute();
        $results[$key] = $stmt->fetchColumn();
      }
      http_response_code(200);
      echo json_encode($results);
    } catch (\PDOException $e) {
      http_response_code(500);
      echo json_encode([
        "message" => "Internal Server Error",
        "error" => $e->getMessage(),
      ]);
    }
  }
}
