<?php
namespace Controller;

use Model\Admin;
use Helpers\encrypt;
use Helpers\decrypt;

class AdminController
{
  private $adminModel;
  private $encrypt;
  private $decrypt;

  public function __construct()
  {
    $this->adminModel = new Admin();
    $this->encrypt = new Encrypt();
    $this->decrypt = new Decrypt();
  }

  public function validate(): array
  {
    $fixedUsername = $_ENV["ADMIN_USERNAME"] ?? null;
    $fixedApiKey = $_ENV["ADMIN_API_KEY"] ?? null;
    $encryptedAPIkey = $this->encrypt->EncryptKey($fixedApiKey);
    if (!$fixedUsername || !$fixedApiKey) {
      return [
        "status" => "error",
        "message" => "Environment variables are not properly set",
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
}
