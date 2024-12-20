<?php
namespace Model;

use DB\DB;
use PDO;

class Reviews
{
  private string $table = "reviews";
  private PDO $DB;

  public function __construct()
  {
    $this->DB = DB::getInstance()->connect();
  }

  public function create(array $data): bool
  {
    // Fetch next review ID for the user
    $query = "SELECT COALESCE(MAX(review_id), 0) + 1 AS next_id 
                  FROM {$this->table} 
                  WHERE user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $data["user_key"]);
    $stmt->execute();
    $nextReviewId = $stmt->fetchColumn();

    // Insert new review
    $query = "INSERT INTO {$this->table} 
                    (review_id, reviewer_name, rating, review_text, user_key,reviewer_job_title) 
                  VALUES 
                    (:review_id, :reviewer_name, :rating, :review_text,
                    :user_key,:reviewer_job_title)";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":review_id", $nextReviewId, PDO::PARAM_INT);
    $stmt->bindParam(":reviewer_name", $data["reviewer_name"]);
    $stmt->bindParam(":rating", $data["rating"], PDO::PARAM_INT);
    $stmt->bindParam(":review_text", $data["review_text"]);
    $stmt->bindParam(":user_key", $data["user_key"]);
    $stmt->bindParam(":reviewer_job_title", $data["reviewer_job_title"]);
    return $stmt->execute();
  }

  public function update(array $data, int $reviewId, string $userKey): bool
  {
    $query = "UPDATE {$this->table} 
                  SET reviewer_name = :reviewer_name, 
                      rating = :rating, 
                      review_text = :review_text
                  WHERE review_id = :review_id AND user_key = :user_key";

    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":reviewer_name", $data["reviewer_name"]);
    $stmt->bindParam(":rating", $data["rating"], PDO::PARAM_INT);
    $stmt->bindParam(":review_text", $data["review_text"]);
    $stmt->bindParam(":review_id", $reviewId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey);

    return $stmt->execute();
  }

  public function findById(int $reviewId, string $userKey): array|false
  {
    $query = "SELECT * FROM {$this->table} WHERE review_id = :review_id AND user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":review_id", $reviewId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey);

    if ($stmt->execute()) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
  }

  public function findAll(
    string $userKey,
    int $limit = 10,
    int $offset = 0
  ): array {
    $limit = $limit > 0 ? $limit : 10;
    $offset = $offset >= 0 ? $offset : 0;
    $query = "SELECT * FROM {$this->table} WHERE user_key = :user_key ORDER BY
    review_id DESC LIMIT :limit OFFSET :offset";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":user_key", $userKey, PDO::PARAM_STR);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

    if ($stmt->execute()) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return [];
  }

  public function delete(int $reviewId, string $userKey): bool
  {
    $query = "DELETE FROM {$this->table} WHERE review_id = :review_id AND user_key = :user_key";
    $stmt = $this->DB->prepare($query);
    $stmt->bindParam(":review_id", $reviewId, PDO::PARAM_INT);
    $stmt->bindParam(":user_key", $userKey);
    return $stmt->execute();
  }
}
