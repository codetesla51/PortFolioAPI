<?php
function myAutoloader($class)
{
  // Define the base directories for different namespaces
  $baseDirs = [
    "DB" => __DIR__ . "/config/", // For DB classes (e.g., DB\Connection)
    "Controller" => __DIR__ . "/src/controllers/", // For Controller classes (e.g., Controller\UserController)
    "Helpers" => __DIR__ . "/src/helpers/", // For Helper classes (e.g., Helpers\StringHelper)
    "Model" => __DIR__ . "/src/models/", // For Model classes (e.g., Model\User)
    "Decrypt" => __DIR__ . "/src/helpers/", // For Decrypt classes (e.g., Decrypt\Decrypt)
    "" => __DIR__ . "/src/", // Default directory for any other classes
  ];

  // Iterate through the base directories to find the correct one
  foreach ($baseDirs as $namespace => $dir) {
    // Check if the class belongs to the namespace
    if ($namespace === "" || strpos($class, $namespace . "\\") === 0) {
      // Remove the namespace prefix (if it exists)
      $class = str_replace($namespace . "\\", "", $class);
      // Replace namespace separator with directory separator
      $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
      // Check if the file exists in the corresponding directory
      $file = $dir . $class . ".php";

      // Load the file if it exists
      if (file_exists($file)) {
        require $file;
        return;
      }
    }
  }

  // If the file is not found in any of the directories
  echo "File not found: $class\n";
}

spl_autoload_register("myAutoloader");
