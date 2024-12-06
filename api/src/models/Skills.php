<?php
namespace Model;
use DB\DB;
use PDO;
use Helpers\decrypt;
class Skills
{
  private string $table = "skills";
  private PDO $DB;
  private Decrypt $decrypt;

  public function __construct()
  {
    $this->DB = (new DB())->connect();
    $this->decrypt = new Decrypt();
  }

  public function create(array $data): bool
  {
    // Fetch next skill ID for the user
    $query = "SELECT COALESCE(MAX(skill_id), 0) + 1 AS next_id 
                  FROM {$this->table} 
                  WHERE user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $data["user_key"]);
    $stmt->execute();
    $nextSkillId = $stmt->fetchColumn();

    // Insert new skill
    $query = "INSERT INTO {$this->table} 
                    (skill_id, skill_name, experience_level, years_of_experience, description, user_key) 
                  VALUES 
                    (:skill_id, :skill_name, :experience_level, :years_of_experience, :description, :user_key)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":skill_id", $nextSkillId, PDO::PARAM_INT);
    $stmt->bindParam(":skill_name", $data["skill_name"]);
    $stmt->bindParam(":experience_level", $data["experience_level"]);
    $stmt->bindParam(":years_of_experience", $data["years_of_experience"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":user_key", $data["user_key"]);
    return $stmt->execute();
  }

  public function update(array $data, int $skillId, string $userKey): bool
  {
    $query = "UPDATE {$this->table} 
                  SET skill_name = :skill_name, 
                      experience_level = :experience_level, 
                      years_of_experience = :years_of_experience, 
                      description = :description
                  WHERE skill_id = :skill_id AND user_key = :user_key";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":skill_name", $data["skill_name"]);
    $stmt->bindParam(":experience_level", $data["experience_level"]);
    $stmt->bindParam(":years_of_experience", $data["years_of_experience"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":skill_id", $skillId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);

    if (!$stmt->execute()) {
      throw new RuntimeException("Failed to execute the update query.");
    }
    return true;
  }

  public function findById(int $skillId, string $userKey): array|false
  {
    $query = "SELECT * FROM {$this->table} WHERE skill_id = :skill_id AND user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":skill_id", $skillId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);

    if ($stmt->execute()) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
  }

  public function findAll(string $userKey): array
  {
    $query = "SELECT * FROM {$this->table} WHERE user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);

    if ($stmt->execute()) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return [];
  }

  public function delete(int $skillId, string $userKey): bool
  {
    $query = "DELETE FROM {$this->table} WHERE skill_id = :skill_id AND user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":skill_id", $skillId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);
    return $stmt->execute();
  }
}
