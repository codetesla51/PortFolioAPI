<?php
use DB\DB;
require_once "./autoader.php";
class Projects
{
  private $table = "projects";
  private $DB;
  private $UserController;

  public function __construct()
  {
    $this->DB = (new DB())->connect();
  }

  public function create(array $data): bool
  {
    $query = "INSERT INTO $this->table
            (title, image, description, tech_stack, start_date, finish_date, github_link, live_link, user_key) 
            VALUES 
            (:title, :image, :description, :tech_stack, :start_date, :finish_date, :github_link, :live_link, :user_key)";

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
    $stmt->bindParam(":user_key", $data["user_key"]);

    return $stmt->execute();
  }

  public function update(array $data, int $id): bool
  {
    $query = "UPDATE $this->table SET 
            title = :title, 
            image = :image, 
            description = :description, 
            tech_stack = :tech_stack, 
            start_date = :start_date, 
            finish_date = :finish_date, 
            github_link = :github_link, 
            live_link = :live_link, 
            user_key = :user_key 
            WHERE id = :id";

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
    $stmt->bindParam(":user_key", $data["user_key"]);
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
  }

  public function fetchByUserKey(string $userKey): array
  {
    $query = "SELECT * FROM $this->table WHERE user_key = :user_key";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $userKey);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function fetchId(int $id): array | false
  {
    $query = "SELECT * FROM $this->table WHERE id = :id";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function delete(int $id): bool
  {
    $query = "DELETE FROM $this->table WHERE id = :id";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
  }
  public function DecryptKey(string $key): string
  {
    $parts = explode("::", base64_decode($key), 2);

    if (count($parts) !== 2) {
      throw new Exception("Invalid encrypted data format");
    }

    [$encryptedData, $iv] = $parts;

    $cipherMethod = "AES-256-CBC";
    $encryptionKey = "usman";

    $decrypted = openssl_decrypt(
      $encryptedData,
      $cipherMethod,
      $encryptionKey,
      0,
      $iv
    );

    if ($decrypted === false) {
      throw new Exception("Decryption failed");
    }

    return $decrypted;
  }

  public function isValidUserKey(string $userKey): bool
  {
    $query = "SELECT user_key FROM users";
    $stmt = $this->DB->query($query);
    $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($keys as $encryptedKey) {
      try {
        $decryptedKey = $this->DecryptKey($encryptedKey);
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
