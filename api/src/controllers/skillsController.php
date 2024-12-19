<?php
use Model\Skills;
class SkillsController
{
  private $skillsModel;
  private $middleware;

  public function __construct()
  {
    $this->skillsModel = new Skills();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function store(): void
  {
    $userKey = $this->middleware->handle();
    if (!$this->middleware->isUnderDailyRequestLimit()) {
      $this->sendResponse(
        429,
        "Request limit reached. Please try again tomorrow."
      );
    }

    $this->middleware->incrementRequestCount();
    $data = json_decode(file_get_contents("php://input"), true);
    $data["skill_name"] = $data["skill_name"] ?? "Untitled";
    $data["experience_level"] = $data["experience_level"] ?? "Beginner";
    $data["years_of_experience"] = $data["years_of_experience"] ?? 0;
    $data["description"] = $data["description"] ?? "No description";
    $data["user_key"] = $userKey;

    // Validate experience level
    $validExperienceLevels = ["Beginner", "Intermediate", "Advanced", "Expert"];
    if (!in_array($data["experience_level"], $validExperienceLevels, true)) {
      http_response_code(400); // Bad Request
      echo json_encode(["error" => "Invalid experience_level value"]);
      return;
    }

    if ($this->skillsModel->create($data)) {
      echo json_encode(["message" => "New Skill added successfully"]);
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to add skill"]);
    }
  }
  public function update(int $id): void
  {
    $userKey = $this->middleware->handle();
    $data = json_decode(file_get_contents("php://input"), true);
    $data["skill_name"] = $data["skill_name"] ?? "Untitled";
    $data["experience_level"] = $data["experience_level"] ?? "Beginner";
    $data["years_of_experience"] = $data["years_of_experience"] ?? 0;
    $data["description"] = $data["description"] ?? "No description";

    $validExperienceLevels = ["Beginner", "Intermediate", "Advanced", "Expert"];
    if (!in_array($data["experience_level"], $validExperienceLevels, true)) {
      http_response_code(400); // Bad Request
      echo json_encode(["error" => "Invalid experience_level value"]);
      return;
    }

    if ($this->skillsModel->update($data, $id, $userKey)) {
      echo json_encode(["message" => "Updated Skill successfully"]);
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to Update skill"]);
    }
  }
  public function index(): void
  {
    $userKey = $this->middleware->handle();

    $projects = $this->skillsModel->findAll($userKey);
    echo json_encode($projects);
  }

  public function show(int $id): void
  {
    $userKey = $this->middleware->handle();
    $project = $this->skillsModel->findById($id, $userKey);
    if ($project) {
      echo json_encode($project);
    } else {
      http_response_code(400); // Bad Request
      echo json_encode([
        "error" => "skill not found",
      ]);
    }
  }

  public function destroy(int $id): void
  {
    $userKey = $this->middleware->handle();

    if ($this->skillsModel->delete($id)) {
      echo json_encode(["message" => "skill deleted successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to delete skill"]);
    }
  }
}
