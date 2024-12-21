<?php
require_once "src/controllers/adminController.php";
require "index.php";
$adminController = new AdminController();

// Call the validate method to create the admin
$response = $adminController->validate();

// Output the response
header("Content-Type: application/json");
echo json_encode($response);
