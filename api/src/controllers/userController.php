<?php
namespace Controller;
use Model\User;
use Helpers\encrypt;
class UserController
{
  private string $name;
  private string $password;
  private $userModel;
  private $encrypt;
  public function __construct(string $name, string $password)
  {
    $this->name = $name;
    $this->password = $password;
    $this->userModel = new User();
    $this->encrypt = new Encrypt();
  }
  public function Validate(): array
  {
    if (empty($this->name) || empty($this->password)) {
      return ["status" => "error", "message" => "All fields are required"];
    }
    if (strlen($this->password) < 8) {
      return [
        "status" => "error",
        "message" => "Password Must Be Longer
             Than 8 character",
      ];
    }
    if (!preg_match("/[A-Z]/", $this->password)) {
      return [
        "status" => "error",
        "message" => "Password Must Contain UpperCase",
      ];
    }
    $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
    $rawAPIkey = bin2hex(random_bytes(16));
    $EncrypredAPIKey = $this->encrypt->EncryptKey($rawAPIkey);
    $insrted = $this->userModel->CreateUser(
      $this->name,
      $hashedPassword,
      $EncrypredAPIKey
    );
    if ($insrted) {
      return [
        "status" => "success",
        "message" => "User registered successfully",
      ];
    } else {
      return ["status" => "error", "message" => "Failed to register user"];
    }
  }
}
