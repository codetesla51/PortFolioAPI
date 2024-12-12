<?php
namespace Controller;
use Model\User;
use Helpers\encrypt;

class UserController
{
  private string $name;
  private string $password;
  private string $email;
  private $userModel;
  private $encrypt;

  public function __construct(string $name, string $password, string $email)
  {
    $this->name = $name;
    $this->password = $password;
    $this->email = $email;
    $this->userModel = new User();
    $this->encrypt = new Encrypt();
  }

  public function validate(): array
  {
    if (empty($this->name) || empty($this->password)) {
      return ["status" => "error", "message" => "All fields are required"];
    }

    if (strlen($this->password) < 8) {
      return [
        "status" => "error",
        "message" => "Password must be longer than 8 characters",
      ];
    }

    if (!preg_match("/[A-Z]/", $this->password)) {
      return [
        "status" => "error",
        "message" => "Password must contain an uppercase letter",
      ];
    }

    $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
    $apiKey = $this->generateApiKey();

    $inserted = $this->userModel->createUser(
      $this->name,
      $this->email,
      $hashedPassword,
      $apiKey["encrypted"]
    );

    if ($inserted) {
      return [
        "status" => "success",
        "message" => "User registered successfully",
        "api_key" => $apiKey["raw"],
      ];
    } else {
      return ["status" => "error", "message" => "Failed to register user"];
    }
  }

  /**
   * Generates and encrypts an API key
   *
   * @return array Contains the raw API key and encrypted API key
   */
  private function generateApiKey(): array
  {
    $rawApiKey = bin2hex(random_bytes(16)); // Generate a 32-character random API key
    $encryptedApiKey = $this->encrypt->EncryptKey($rawApiKey);

    return [
      "raw" => $rawApiKey,
      "encrypted" => $encryptedApiKey,
    ];
  }
}
