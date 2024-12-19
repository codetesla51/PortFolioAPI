<?php
require_once "autoader.php";
require_once "index.php";
use Controller\adminController;

$username = "usman";

$adminController = new AdminController();

$response = $adminController->validate();

header("Content-Type: application/json");
echo json_encode($response);
