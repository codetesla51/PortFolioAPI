<?php
require __DIR__ . "/src/controllers/userController.php";

$name = "JohnDoe";
$password = "Password123";

$userController = new UserController($name, $password);

$result = $userController->validate();

header("Content-Type: application/json");
echo json_encode($result);
