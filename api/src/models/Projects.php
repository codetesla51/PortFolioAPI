<?php
namespace Model;
use DB\DB;
use PDO;
class Projects
{
  private $table = "projects";
  private PDO $DB;
  private $UserController;

  public function __construct()
  {
    $this->DB = DB::getInstance()->connect();
  }

  public function create(array $data): bool
  {
    // Get the next project_id for the given user_key
    $query = "SELECT COALESCE(MAX(project_id), 0) + 1 AS next_id 
              FROM {$this->table} 
              WHERE user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $data["user_key"]);
    $stmt->execute();
    $nextProjectId = $stmt->fetchColumn();

    // Insert the new project with the calculated project_id
    $query = "INSERT INTO {$this->table}
            (project_id, title, image, description, tech_stack, start_date, finish_date, github_link, live_link, user_key) 
            VALUES 
            (:project_id, :title, :image, :description, :tech_stack, :start_date, :finish_date, :github_link, :live_link, :user_key)";

    $stmt = $this->DB->prepare($query);

    // Bind parameters
    $stmt->bindParam(":project_id", $nextProjectId);
    $stmt->bindParam(":title", $data["title"]);
    $stmt->bindParam(":image", $data["image"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":tech_stack", $data["tech_stack"]);
    $stmt->bindParam(":start_date", $data["start_date"]);
    $stmt->bindParam(":finish_date", $data["finish_date"]);
    $stmt->bindParam(":github_link", $data["github_link"]);
    $stmt->bindParam(":live_link", $data["live_link"]);
    $stmt->bindParam(":user_key", $data["user_key"]);

    return $stmt->execute();
  }

  public function update(array $data, int $projectId, string $userKey): bool
  {
    $query = "UPDATE {$this->table} SET 
              title = :title, 
              image = :image, 
              description = :description, 
              tech_stack = :tech_stack, 
              start_date = :start_date, 
              finish_date = :finish_date, 
              github_link = :github_link, 
              live_link = :live_link
              WHERE project_id = :project_id AND user_key = :user_key";

    $stmt = $this->DB->prepare($query);

    // Bind parameters
    $stmt->bindParam(":title", $data["title"]);
    $stmt->bindParam(":image", $data["image"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":tech_stack", $data["tech_stack"]);
    $stmt->bindParam(":start_date", $data["start_date"]);
    $stmt->bindParam(":finish_date", $data["finish_date"]);
    $stmt->bindParam(":github_link", $data["github_link"]);
    $stmt->bindParam(":live_link", $data["live_link"]);
    $stmt->bindParam(":project_id", $projectId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);

    if (!$stmt->execute()) {
      throw new RuntimeException("Failed to execute the update query.");
    }

    return true;
  }

  public function findAll(
    string $userKey,
    int $limit = 10,
    int $offset = 0
  ): array {
    $limit = $limit > 0 ? $limit : 10;
    $offset = $offset >= 0 ? $offset : 0;

    $query = "SELECT * FROM {$this->table} WHERE user_key = :user_key ORDER BY project_id DESC LIMIT :limit OFFSET :offset";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

    if ($stmt->execute()) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return [];
  }

  public function fetchId(int $projectId, string $userKey): array|false
  {
    $query = "SELECT * FROM {$this->table} WHERE project_id = :project_id AND
    user_key = :user_key ORDER BY project_id DESC";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":project_id", $projectId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);

    if ($stmt->execute()) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
  }

  public function delete(int $projectId, string $userKey): bool
  {
    $query = "DELETE FROM {$this->table} WHERE project_id = :project_id AND user_key = :user_key";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":project_id", $projectId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);

    return $stmt->execute();
  }
}
