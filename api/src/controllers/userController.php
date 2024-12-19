<?php

use Model\User;
use Helpers\encrypt;
use Helpers\decrypt;

class UserController
{
  private array $inputData;
  private $userModel;
  private $encrypt;
  private $decrypt;

  public function __construct()
  {
    $this->inputData =
      json_decode(file_get_contents("php://input"), true) ?? [];
    $this->userModel = new User();
    $this->encrypt = new Encrypt();
    $this->decrypt = new Decrypt();
  }

  /**
   * Handles user registration
   */
  public function register(): void
  {
    $validationResult = $this->validateInput();

    if ($validationResult["status"] === "error") {
      $this->sendResponse(400, $validationResult);
      return;
    }

    $hashedPassword = password_hash(
      $this->inputData["password"],
      PASSWORD_BCRYPT
    );
    $apiKey = $this->generateApiKey();
    $inserted = $this->userModel->createUser(
      $this->inputData["name"],
      $this->inputData["email"],
      $hashedPassword,
      $apiKey["encrypted"]
    );

    if ($inserted) {
      $user = $this->userModel->getUserByEmail($this->inputData["email"]);

      $this->sendResponse(201, [
        "status" => "success",
        "message" => "User registered successfully",
        "username" => $user["user_name"],
        "api_key" => $apiKey["raw"],
        "email" => $user["user_email"],
      ]);
    } else {
      $this->sendResponse(500, [
        "status" => "error",
        "message" => "Failed to register user",
      ]);
    }
  }
  /**
   * Validates user input
   */
  private function validateInput(): array
  {
    if (
      empty($this->inputData["name"]) ||
      empty($this->inputData["password"]) ||
      empty($this->inputData["email"])
    ) {
      return ["status" => "error", "message" => "All fields are required"];
    }

    if ($this->userModel->isEmailTaken($this->inputData["email"])) {
      return ["status" => "error", "message" => "Email is already taken"];
    }

    if (strlen($this->inputData["password"]) < 8) {
      return [
        "status" => "error",
        "message" => "Password must be longer than 8 characters",
      ];
    }

    if (!preg_match("/[A-Z]/", $this->inputData["password"])) {
      return [
        "status" => "error",
        "message" => "Password must contain an uppercase letter",
      ];
    }

    return ["status" => "success"];
  }

  /**
   * Generates and encrypts an API key
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

  /**
   * Handles user login
   */
  public function login(): void
  {
    if (
      empty($this->inputData["email"]) ||
      empty($this->inputData["password"])
    ) {
      $this->sendResponse(400, [
        "status" => "error",
        "message" => "Email and password are required",
      ]);
      return;
    }

    $user = $this->userModel->getUserByEmail($this->inputData["email"]);

    if (
      !$user ||
      !password_verify($this->inputData["password"], $user["user_password"])
    ) {
      $this->sendResponse(401, [
        "status" => "error",
        "message" => "Invalid email or password",
      ]);
      return;
    }

    $encApi = $user["user_key"];
    $decApi = $this->decrypt->DecryptKey($encApi);

    $email = $user["user_email"] ?? "Unknown email";
    $this->sendResponse(200, [
      "status" => "success",
      "message" => "Login successful",
      "username" => $user["user_name"],
      "api_key" => $decApi,
      "email" => $email,
    ]);
  }

  /**
   * Regenerates the API key for the user
   */
  public function regenerateAPIKey(): void
  {
    if (
      empty($this->inputData["email"]) ||
      empty($this->inputData["api_key"])
    ) {
      $this->sendResponse(400, [
        "status" => "error",
        "message" => "Email and current API key are required",
      ]);
      return;
    }

    $user = $this->userModel->getUserByEmail($this->inputData["email"]);

    if (!$user) {
      $this->sendResponse(404, [
        "status" => "error",
        "message" => "User not found",
      ]);
      return;
    }

    $storedApiKey = $this->decrypt->DecryptKey($user["user_key"]);
    if ($storedApiKey !== $this->inputData["api_key"]) {
      $this->sendResponse(401, [
        "status" => "error",
        "message" => "Invalid current API key",
      ]);
      return;
    }

    // Generate a new API key
    $newApiKey = $this->generateApiKey();

    // Update the user's API key in the database
    $updated = $this->userModel->updateApiKey(
      $user["id"],
      $newApiKey["encrypted"]
    );

    if ($updated) {
      $this->sendResponse(200, [
        "status" => "success",
        "message" => "API key regenerated successfully",
        "new_api_key" => $newApiKey["raw"],
      ]);
    } else {
      $this->sendResponse(500, [
        "status" => "error",
        "message" => "Failed to regenerate API key",
      ]);
    }
  } /**
   * Sends an HTTP response with status code and data
   */
  private function sendResponse(int $statusCode, array $data): void
  {
    http_response_code($statusCode);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit();
  }
}
