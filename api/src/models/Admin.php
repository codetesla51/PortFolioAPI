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
  public function CreateAdmin(string $username, string $key): bool
  {
    $query = "INSERT INTO admins (username, user_key) 
              VALUES (:username, :key)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":key", $key);
    return $stmt->execute();
  }
}
