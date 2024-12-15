<?php

use Model\User;
use Helpers\encrypt;

class UserController
{
  private array $inputData;
  private $userModel;
  private $encrypt;

  public function __construct()
  {
    $this->inputData =
      json_decode(file_get_contents("php://input"), true) ?? [];
    $this->userModel = new User();
    $this->encrypt = new Encrypt();
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
      $this->sendResponse(201, [
        "status" => "success",
        "message" => "User registered successfully",
        "api_key" => $apiKey["raw"],
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

    $this->sendResponse(200, [
      "status" => "success",
      "message" => "Login successful",
      "api_key" => $user["user_key"],
    ]);
  }

  /**
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
