<?php
require_once "src/controllers/projectController.php";
$routes = require "src/routes/api.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = strtok($_SERVER["REQUEST_URI"], "?");

error_log("Requested Method: $requestMethod, Requested URI: $requestUri");

if (isset($routes[$requestMethod])) {
  foreach ($routes[$requestMethod] as $route => $handler) {
    $pattern = preg_replace("/\{[^\}]+\}/", "([^/]+)", $route);
    if (preg_match("#^" . $pattern . "$#", $requestUri, $matches)) {
      array_shift($matches);
      [$controller, $method] = explode("@", $handler);

      error_log(
        "Matched Route: $route, Controller: $controller, Method: $method"
      );

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
