<?php
require "autoader.php";
use Controller\userController;
$name = "JohnDoe";
$password = "Password123";
$email = "uoladele99@gmail.com";
$userController = new UserController($name, $password, $email);

$result = $userController->validate();

header("Content-Type: application/json");
echo json_encode($result);
