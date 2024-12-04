<?php
use DB\DB;
require_once "./autoader.php";
class User
{
  private $DB;
  public function __construct()
  {
    $this->DB = (new DB())->connect();
  }
  public function CreateUser($name, $password, $key): bool
  {
    $query = "INSERT INTO users (user_name, user_password,user_key) VALUES (:name,
      :password,:key)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":key", $key);
    return $stmt->execute();
  }
}
