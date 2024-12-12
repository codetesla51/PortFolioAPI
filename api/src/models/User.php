<?php
namespace Model;
use DB\DB;
class User
{
  private $DB;
  public function __construct()
  {
    $this->DB = (new DB())->connect();
  }
  public function CreateUser($name, $email, $password, $key): bool
  {
    $query = "INSERT INTO users (user_name, user_password, user_email, user_key) 
              VALUES (:name, :password, :email, :key)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":key", $key);
    return $stmt->execute();
  }

  public function isEmailTaken(string $email): bool
  {
    $query = "SELECT COUNT(*) as count FROM users WHERE user_email = :email";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result["count"] > 0;
  }
  public function getEmailByApiKey(string $apiKey): ?string
  {
    $query = "SELECT user_email FROM users WHERE user_key = :apiKey";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":apiKey", $apiKey);

    if ($stmt->execute()) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result["user_email"] ?? null; 
    }

    return null;
  }
}
