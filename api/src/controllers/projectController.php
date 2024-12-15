<?php
use Model\Projects;

class ProjectController
{
  private $ProjectModel;
  private $middleware;

  public function __construct()
  {
    $this->ProjectModel = new Projects();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function store(): void
  {
    $userKey = $this->middleware->handle();
    if (!$this->middleware->isUnderDailyRequestLimit()) {
      $this->sendResponse(
        429 , ["error" => "Request limit reached. Please try again tomorrow."]
      );
    }

    $this->middleware->incrementRequestCount();

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
      $this->sendResponse(200, ["message" => "Project created successfully"]);
    } else {
      $this->sendResponse(500, ["error" => "Failed to create project"]);
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

    if ($this->ProjectModel->update($data, $id, $userKey)) {
      $this->sendResponse(200, ["message" => "Project updated successfully"]);
    } else {
      $this->sendResponse(500, ["error" => "Failed to update project"]);
    }
  }

  public function index(): void
  {
    $userKey = $this->middleware->handle();
    $projects = $this->ProjectModel->fetchByUserKey($userKey);
    $this->sendResponse(200, $projects);
  }

  public function show(int $id): void
  {
    $userKey = $this->middleware->handle();
    $project = $this->ProjectModel->fetchId($id, $userKey);
    if ($project) {
      $this->sendResponse(200, $project);
    } else {
      $this->sendResponse(404, ["error" => "Project not found"]);
    }
  }

  public function destroy(int $id): void
  {
    $userKey = $this->middleware->handle();
    if ($this->ProjectModel->delete($id)) {
      $this->sendResponse(200, ["message" => "Project deleted successfully"]);
    } else {
      $this->sendResponse(500, ["error" => "Failed to delete project"]);
    }
  }

  private function sendResponse(int $statusCode, array $data): void
  {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
  }
}