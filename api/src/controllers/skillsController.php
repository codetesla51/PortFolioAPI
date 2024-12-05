<?php
require __DIR__ . "../../models/Skills.php";
use Helpers\decrypt;
use DB\DB;

class SkillsController
{
  private $skillsModel;
  private $decrypt;
  private $DB;
  public function __construct()
  {
    $this->skillsModel = new Skills();
    $this->decrypt = new Decrypt();
    $this->DB = (new DB())->connect();
  }

  public function store(): void
  {
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    // Check for API key
    if (!$userKey) {
      http_response_code(401); // Unauthorized
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Validate API key
    if (!$this->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Parse input data
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

    // Attempt to create the skill
    if ($this->skillsModel->create($data)) {
      echo json_encode(["message" => "New Skill added successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to add skill"]);
    }
  }
  public function update(int $id): void
  {
    $headers = getallheaders();
    $userKey = $headers["API-Key"] ?? null;

    if (!$userKey) {
      http_response_code(401);
      echo json_encode(["error" => "API key is required"]);
      return;
    }

    // Validate API key
    if (!$this->isValidUserKey($userKey)) {
      http_response_code(403); // Forbidden
      echo json_encode(["error" => "Invalid API key"]);
      return;
    }

    // Parse input data
    $data = json_decode(file_get_contents("php://input"), true);
    $data["skill_name"] = $data["skill_name"] ?? "Untitled";
    $data["experience_level"] = $data["experience_level"] ?? "Beginner";
    $data["years_of_experience"] = $data["years_of_experience"] ?? 0;
    $data["description"] = $data["description"] ?? "No description";

    // Validate experience level
    $validExperienceLevels = ["Beginner", "Intermediate", "Advanced", "Expert"];
    if (!in_array($data["experience_level"], $validExperienceLevels, true)) {
      http_response_code(400); // Bad Request
      echo json_encode(["error" => "Invalid experience_level value"]);
      return;
    }

    if ($this->skillsModel->update($data, $id,$userKey)) {
      echo json_encode(["message" => "Updated Skill successfully"]);
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to Update skill"]);
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

    $projects = $this->skillsModel->findAll($userKey);
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

    if ($this->skillsModel->delete($id)) {
      echo json_encode(["message" => "skill deleted successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to delete skill"]);
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
