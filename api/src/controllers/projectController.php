<?php
require_once "./autoader.php";

use Model\Projects;
use Helpers\decrypt;
use DB\DB;
class ProjectController
{
  private $ProjectModel;
  private $decrypt;
  private $DB;
  public function __construct()
  {
    $this->ProjectModel = new Projects();
    $this->decrypt = new Decrypt();
    $this->DB = (new DB())->connect();
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
    if (!$this->isValidUserKey($userKey)) {
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

  public function update(int $id): void
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
    if (!$this->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Decode incoming data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
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
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

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
    if (!$this->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Fetch project by ID
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
    // Retrieve the API key from headers
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Verify the API key
    if (!$this->isValidUserKey($userKey)) {
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
  public function isValidUserKey(string $userKey): bool
  {
    $query = "SELECT user_key FROM users";
    $stmt = $this->DB->query($query);
    $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($keys as $encryptedKey) {
      try {
        $decryptedKey = $this->decrypt->DecryptKey($encryptedKey);
        if ($decryptedKey === $userKey) {
          return true;
        }
      } catch (Exception $e) {
        echo "Failed to decrypt key: {$e->getMessage()}\n";
      }
    }

    return false;
  }
}
