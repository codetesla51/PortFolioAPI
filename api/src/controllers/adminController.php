<?php
namespace Controller;
use Model\Admin;
use Helpers\encrypt;

class AdminController
{
  private string $username;
  private $adminModel;
  private $encrypt;

  public function __construct(string $username)
  {
    $this->username = $username;
    $this->adminModel = new Admin();
    $this->encrypt = new Encrypt();
  }

  public function validate(): array
  {
    if (empty($this->username)) {
      return ["status" => "error", "message" => "All fields are required"];
    }

    $apiKey = $this->generateApiKey();
    $inserted = $this->adminModel->CreateAdmin(
      $this->username,
      $apiKey["encrypted"]
    );

    if ($inserted) {
      return [
        "status" => "success",
        "message" => "Admin registered successfully",
        "api_key" => $apiKey["raw"],
      ];
    } else {
      return ["status" => "error", "message" => "Failed to register Admin"];
    }
  }

  private function generateApiKey(): array
  {
    $rawApiKey = bin2hex(random_bytes(16));
    $encryptedApiKey = $this->encrypt->EncryptKey($rawApiKey);

    return [
      "raw" => $rawApiKey,
      "encrypted" => $encryptedApiKey,
    ];
  }
}
