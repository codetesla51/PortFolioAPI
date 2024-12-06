<?php

use Model\Reviews;

class ReviewsController
{
  private $reviewsModel;
  private $middleware;

  public function __construct()
  {
    $this->reviewsModel = new Reviews();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function store(): void
  {
    $userKey = $this->middleware->handle();
    $data = json_decode(file_get_contents("php://input"), true);
    $data["reviewer_name"] = $data["reviewer_name"] ?? "Anonymous";
    $data["rating"] = $data["rating"] ?? 0;
    $data["review_text"] = $data["review_text"] ?? "No review provided";
    $data["user_key"] = $userKey;

    // Validate rating
    if ($data["rating"] < 0 || $data["rating"] > 5) {
      http_response_code(400); // Bad Request
      echo json_encode(["error" => "Invalid rating value. It must be between 0 and 5."]);
      return;
    }

    if ($this->reviewsModel->create($data)) {
      echo json_encode(["message" => "Review added successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to add review"]);
    }
  }

  public function update(int $id): void
  {
    $userKey = $this->middleware->handle();
    $data = json_decode(file_get_contents("php://input"), true);
    $data["reviewer_name"] = $data["reviewer_name"] ?? "Anonymous";
    $data["rating"] = $data["rating"] ?? 0;
    $data["review_text"] = $data["review_text"] ?? "No review provided";

    if ($data["rating"] < 0 || $data["rating"] > 5) {
      http_response_code(400); // Bad Request
      echo json_encode(["error" => "Invalid rating value. It must be between 0 and 5."]);
      return;
    }

    if ($this->reviewsModel->update($data, $id, $userKey)) {
      echo json_encode(["message" => "Review updated successfully"]);
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to update review"]);
    }
  }

  public function index(): void
  {
    $userKey = $this->middleware->handle();
    $reviews = $this->reviewsModel->findAll($userKey);
    echo json_encode($reviews);
  }

  public function show(int $id): void
  {
    $userKey = $this->middleware->handle();
    $review = $this->reviewsModel->findById($id, $userKey);

    if ($review) {
      echo json_encode($review);
    } else {
      http_response_code(404); // Not Found
      echo json_encode(["error" => "Review not found"]);
    }
  }

  public function destroy(int $id): void
  {
    $userKey = $this->middleware->handle();

    if ($this->reviewsModel->delete($id, $userKey)) {
      echo json_encode(["message" => "Review deleted successfully"]);
    } else {
      http_response_code(500); // Internal Server Error
      echo json_encode(["error" => "Failed to delete review"]);
    }
  }
}
