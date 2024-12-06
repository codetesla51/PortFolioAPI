<?php
function myAutoloader($class)
{
  // Define the base directories for different namespaces
  $baseDirs = [
    "DB" => __DIR__ . "/config/",
    "Controller" => __DIR__ . "/src/controllers/",
    "Helpers" => __DIR__ . "/src/helpers/",
    "Model" => __DIR__ . "/src/models/",
    "Decrypt" => __DIR__ . "/src/helpers/",
    "Middleware" => __DIR__ . "/src/middleware/",
    "" => __DIR__ . "/src/",
  ];

  // Special mapping for class name mismatches
  $specialCases = [
    "Middleware\\ApiKeyMiddleware" =>
      __DIR__ . "/src/middleware/ValidateUserKeyMiddleware.php",
  ];

  // Check special cases first
  if (isset($specialCases[$class])) {
    $file = $specialCases[$class];
    if (file_exists($file)) {
      require $file;
      return;
    } else {
      return;
    }
  }

  // Regular namespace-based autoloading
  foreach ($baseDirs as $namespace => $dir) {
    if ($namespace === "" || strpos($class, $namespace . "\\") === 0) {
      $class = str_replace($namespace . "\\", "", $class);
      $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
      $file = $dir . $class . ".php";

      if (file_exists($file)) {
        require $file;
        return;
      }
    }
  }

  echo "File not found: $class\n";
}

spl_autoload_register("myAutoloader");
