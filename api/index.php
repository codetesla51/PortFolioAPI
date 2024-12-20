<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit();
}
use Dotenv\Dotenv;
require_once "autoader.php";
require_once "vendor/autoload.php";
require_once "src/controllers/projectController.php";
require_once "src/controllers/resetController.php";
require_once "src/controllers/skillsController.php";
require_once "src/controllers/reviewsController.php";
require_once "src/controllers/experienceController.php";
require_once "src/controllers/emailController.php";
require_once "src/controllers/userController.php";
$routes = require "src/routes/api.php";
if (file_exists(__DIR__ . "/.env")) {
  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->load();
}
$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = strtok($_SERVER["REQUEST_URI"], "?");

if (isset($routes[$requestMethod])) {
  foreach ($routes[$requestMethod] as $route => $handler) {
    $pattern = preg_replace("/\{[^\}]+\}/", "([^/]+)", $route);
    if (preg_match("#^" . $pattern . "$#", $requestUri, $matches)) {
      array_shift($matches);
      [$controller, $method] = explode("@", $handler);

      if (class_exists($controller)) {
        $controllerInstance = new $controller();
        echo call_user_func_array([$controllerInstance, $method], $matches);
        exit();
      } else {
        http_response_code(500);
        echo json_encode(["error" => "Controller $controller not found"]);
        exit();
      }
    }
  }
}

http_response_code(404);
echo json_encode(["error" => "Route not found"]);
