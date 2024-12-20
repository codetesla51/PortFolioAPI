<?php
namespace Model;
use DB\DB;
use PDO;
class Email
{
  private string $table = "users";
  private PDO $DB;
  public function __construct()
  {
    $this->DB = DB::getInstance()->connect();
  }
  public function ValidateEmailFromRequest(string $email): bool
  {
    $query = "SELECT user_email FROM users WHERE user_email = :email";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? true : false;
  }
  public function GetUserNameFromRequest(string $email): ?string
  {
    $query = "SELECT user_name FROM users WHERE user_email = :email";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);

    if ($stmt->execute()) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result["user_name"] ?? null;
    }

    throw new Exception("Database query failed.");
  }
}
