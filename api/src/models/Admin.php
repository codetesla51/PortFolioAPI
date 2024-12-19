<?php
namespace Model;

use DB\DB;

class Admin
{
  private $DB;

  public function __construct()
  {
    $this->DB = (new DB())->connect();
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
}
