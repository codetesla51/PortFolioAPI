<?php
namespace Model;

use DB\DB;
use PDO;

class Experience
{
  private string $table = "experiences";
  private PDO $DB;

  public function __construct()
  {
    $this->DB = (new DB())->connect();
  }

  public function create(array $data): bool
  {
    // Fetch next experience ID for the user
    $query = "SELECT COALESCE(MAX(experience_id), 0) + 1 AS next_id 
                  FROM {$this->table} 
                  WHERE user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $data["user_key"], PDO::PARAM_STR);
    $stmt->execute();
    $nextExperienceId = $stmt->fetchColumn();

    // Insert new experience
    $query = "INSERT INTO {$this->table} 
                  (experience_id, user_key, company_name, role, start_date, end_date, description) 
                  VALUES 
                  (:experience_id, :user_key, :company_name, :role, :start_date, :end_date, :description)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":experience_id", $nextExperienceId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $data["user_key"]);
    $stmt->bindParam(":company_name", $data["company_name"]);
    $stmt->bindParam(":role", $data["role"]);
    $stmt->bindParam(":start_date", $data["start_date"]);
    $stmt->bindParam(":end_date", $data["end_date"]);
    $stmt->bindParam(":description", $data["description"]);

    return $stmt->execute();
  }

  public function update(array $data, int $experienceId, string $userKey): bool
  {
    $query = "UPDATE {$this->table} 
                  SET company_name = :company_name, 
                      role = :role, 
                      start_date = :start_date, 
                      end_date = :end_date,
                      description = :description
                  WHERE experience_id = :experience_id AND user_key = :user_key";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":company_name", $data["company_name"]);
    $stmt->bindParam(":role", $data["role"]);
    $stmt->bindParam(":start_date", $data["start_date"]);
    $stmt->bindParam(":end_date", $data["end_date"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":experience_id", $experienceId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey);

    return $stmt->execute();
  }

  public function findById(int $experienceId, string $userKey): array|false
  {
    $query = "SELECT * FROM {$this->table} WHERE experience_id = :experience_id AND user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":experience_id", $experienceId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey);

    if ($stmt->execute()) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
  }

  public function findAll(string $userKey): array
  {
    $query = "SELECT * FROM {$this->table} WHERE user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $userKey);

    if ($stmt->execute()) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return [];
  }

  public function delete(int $experienceId, string $userKey): bool
  {
    $query = "DELETE FROM {$this->table} WHERE experience_id = :experience_id AND user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":experience_id", $experienceId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey);
    return $stmt->execute();
  }
}
