<?php
require __DIR__ . "../../models/Projects.php";

class ProjectController
{
  private $ProjectModel;

  public function __construct()
  {
    $this->ProjectModel = new Projects();
  }

  public function store(): void
  {
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Verify API key
    if (!$this->ProjectModel->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Decode incoming data
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

  public function update(): void
  {
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Verify API key
    if (!$this->ProjectModel->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Get project ID from the request
    $id = $_GET["id"] ?? null;
    if (!$id) {
      http_response_code(400); // Bad Request
      echo json_encode(["error" => "Project ID is required"]);
      return;
    }

    // Decode incoming data
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

    if ($this->ProjectModel->update($data, $id)) {
      echo json_encode(["message" => "Project updated successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to update project"]);
    }
  }

  public function index(): void
  {
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Fetch user-specific projects
    $projects = $this->ProjectModel->fetchByUserKey($userKey);
    echo json_encode($projects);
  }

  public function show(int $id): void
  {
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Verify the API key
    if (!$this->ProjectModel->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Fetch project by ID
    $project = $this->ProjectModel->fetchId($id);
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
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Verify the API key
    if (!$this->ProjectModel->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    if ($this->ProjectModel->delete($id)) {
      echo json_encode(["message" => "Project deleted successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to delete project"]);
    }
  }
}
