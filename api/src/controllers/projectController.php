<?php
use Model\Projects;
class ProjectController
{
  private $ProjectModel;
  private $DB;
  private $middleware;
  public function __construct()
  {
    $this->ProjectModel = new Projects();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function store(): void
  {
    $userKey = $this->middleware->handle();

    $data = json_decode(file_get_contents("php://input"), true);

    $data["title"] = $data["title"] ?? "Untitled Project";
    $data["image"] = $data["image"] ?? null;
    $data["description"] = $data["description"] ?? null;
    $data["tech_stack"] = isset($data["tech_stack"])
      ? json_encode($data["tech_stack"])
      : null;
    $data["start_date"] = $data["start_date"] ?? null;
    $data["finish_date"] = $data["finish_date"] ?? null;
    $data["user_key"] = $userKey;
    $data["github_link"] = $data["github_link"] ?? null;
    $data["live_link"] = $data["live_link"] ?? null;

    if ($this->ProjectModel->create($data)) {
      echo json_encode(["message" => "Project created successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to create project"]);
    }
  }

  public function update(int $id): void
  {
    $userKey = $this->middleware->handle();
    $data = json_decode(file_get_contents("php://input"), true);

    $data["title"] = $data["title"] ?? "Untitled Project";
    $data["image"] = $data["image"] ?? null;
    $data["description"] = $data["description"] ?? null;
    $data["tech_stack"] = isset($data["tech_stack"])
      ? json_encode($data["tech_stack"])
      : null;
    $data["start_date"] = $data["start_date"] ?? null;
    $data["finish_date"] = $data["finish_date"] ?? null;
    $data["user_key"] = $userKey;
    $data["github_link"] = $data["github_link"] ?? null;
    $data["live_link"] = $data["live_link"] ?? null;

    // Perform the update
    if ($this->ProjectModel->update($data, $id, $userKey)) {
      echo json_encode(["message" => "Project updated successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to update project"]);
    }
  }

  public function index(): void
  {
    $userKey = $this->middleware->handle();
    $projects = $this->ProjectModel->fetchByUserKey($userKey);
    echo json_encode($projects);
  }

  public function show(int $id): void
  {
    $userKey = $this->middleware->handle();
    $project = $this->ProjectModel->fetchId($id, $userKey);
    if ($project) {
      echo json_encode($project);
    } else {
      http_response_code(400); // Bad Request
      echo json_encode([
        "error" => "Project not found",
      ]);
    }
  }

  public function destroy(int $id): void
  {
    $userKey = $this->middleware->handle();
    if ($this->ProjectModel->delete($id)) {
      echo json_encode(["message" => "Project deleted successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to delete project"]);
    }
  }
}
