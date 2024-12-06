<?php

use Model\Experience;

class ExperienceController
{
  private $experienceModel;
  private $middleware;

  public function __construct()
  {
    $this->experienceModel = new Experience();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function store(): void
  {
    $userKey = $this->middleware->handle();
    $data = json_decode(file_get_contents("php://input"), true);
    $data["company_name"] = $data["company_name"] ?? "Unknown Company";
    $data["role"] = $data["role"] ?? "Unknown Role";
    $data["start_date"] = $data["start_date"] ?? null;
    $data["end_date"] = $data["end_date"] ?? null;
    $data["description"] = $data["description"] ?? "No description provided";
    $data["user_key"] = $userKey;

    // Validate required fields
    if (
      empty($data["company_name"]) ||
      empty($data["role"]) ||
      empty($data["start_date"])
    ) {
      http_response_code(400); // Bad Request
      echo json_encode([
        "error" => "Company name, role, and start date are required fields.",
      ]);
      return;
    }

    if ($this->experienceModel->create($data)) {
      echo json_encode(["message" => "Experience added successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to add experience"]);
    }
  }

  public function update(int $id): void
  {
    $userKey = $this->middleware->handle();
    $data = json_decode(file_get_contents("php://input"), true);
    $data["company_name"] = $data["company_name"] ?? "Unknown Company";
    $data["role"] = $data["role"] ?? "Unknown Role";
    $data["start_date"] = $data["start_date"] ?? null;
    $data["end_date"] = $data["end_date"] ?? null;
    $data["description"] = $data["description"] ?? "No description provided";

    if (
      empty($data["company_name"]) ||
      empty($data["role"]) ||
      empty($data["start_date"])
    ) {
      http_response_code(400); // Bad Request
      echo json_encode([
        "error" => "Company name, role, and start date are required fields.",
      ]);
      return;
    }

    if ($this->experienceModel->update($data, $id, $userKey)) {
      echo json_encode(["message" => "Experience updated successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to update experience"]);
    }
  }

  public function index(): void
  {
    $userKey = $this->middleware->handle();
    $experiences = $this->experienceModel->findAll($userKey);
    echo json_encode($experiences);
  }

  public function show(int $id): void
  {
    $userKey = $this->middleware->handle();
    $experience = $this->experienceModel->findById($id, $userKey);

    if ($experience) {
      echo json_encode($experience);
    } else {
      http_response_code(404); // Not Found
      echo json_encode(["error" => "Experience not found"]);
    }
  }

  public function destroy(int $id): void
  {
    $userKey = $this->middleware->handle();

    if ($this->experienceModel->delete($id, $userKey)) {
      echo json_encode(["message" => "Experience deleted successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to delete experience"]);
    }
  }
}
