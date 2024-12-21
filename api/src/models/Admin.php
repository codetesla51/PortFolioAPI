<?php
namespace Model;

use DB\DB;

class Admin
{
  public $DB;

  private $userTable = "users";
  private $logTable = "log";
  private $projectTable = "projects";
  private $skillsTable = "skills";
  private $reviewsTable = "reviews";
  private $experienceTable = "experiences";

  public function __construct()
  {
    $this->DB = DB::getInstance()->connect();
  }

  public function getAdminByUsername(string $username): ?array
  {
    $query = "SELECT * FROM admins WHERE username = :username";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $result = $stmt->fetch();
    return $result ?: null;
  }
  public function createAdmin(string $username, string $key): bool
  {
    $query = "INSERT INTO admins (username, user_key) 
                  VALUES (:username, :key)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":key", $key);
    return $stmt->execute();
  }
  public function CountData(): array
  {
    $queries = [
      "total_users" => "SELECT COUNT(*) as total_users FROM {$this->userTable}",
      "total_emails_sent" => "SELECT SUM(emailsentToday) as total_emails FROM {$this->userTable}",
      "total_requests" => "SELECT SUM(normalRequestToday) as total_requests FROM {$this->userTable}",
      "log_requests" => "SELECT COUNT(ip) as log_requests FROM {$this->logTable}",
      "total_projects" => "SELECT COUNT(*) as total_projects FROM {$this->projectTable}",
      "total_skills" => "SELECT COUNT(*) as total_skills FROM {$this->skillsTable}",
      "total_reviews" => "SELECT COUNT(*) as total_reviews FROM {$this->reviewsTable}",
      "total_experiences" => "SELECT COUNT(*) as total_experiences FROM {$this->experienceTable}",
    ];

    return $queries;
  }
}
