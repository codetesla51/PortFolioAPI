<?php
require __DIR__ . "../../models/User.php";
class UserController
{
  private string $name;
  private string $password;
  private $userModel;
  public function __construct(string $name, string $password)
  {
    $this->name = $name;
    $this->password = $password;
    $this->userModel = new User();
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
    $EncrypredAPIKey = $this->EncryptKey($rawAPIkey);
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
  public function EncryptKey(string $key): string
  {
    $cipherMethod = "AES-256-CBC";

    $ivLength = openssl_cipher_iv_length($cipherMethod);
    $iv = openssl_random_pseudo_bytes($ivLength);

    $encryptionKey = "usman";

    $encryptedData = openssl_encrypt(
      $key,
      $cipherMethod,
      $encryptionKey,
      0,
      $iv
    );

    if ($encryptedData === false) {
      throw new Exception("API key encryption failed");
    }

    $encryptedDataWithIv = base64_encode($encryptedData . "::" . $iv);
    return $encryptedDataWithIv;
  }

  public function DecryptKey(string $key): string
  {
    $parts = explode("::", base64_decode($key), 2);

    if (count($parts) !== 2) {
      throw new Exception("Invalid encrypted data format");
    }

    [$encryptedData, $iv] = $parts;

    $cipherMethod = "AES-256-CBC";
    $encryptionKey = "usman";

    $decrypted = openssl_decrypt(
      $encryptedData,
      $cipherMethod,
      $encryptionKey,
      0,
      $iv
    );

    if ($decrypted === false) {
      throw new Exception("Decryption failed");
    }

    return $decrypted;
  }
}
