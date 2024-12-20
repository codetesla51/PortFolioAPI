<?php
function myAutoloader($class)
{
  $baseDirs = [
    "DB" => __DIR__ . "/config/",
    "Controller" => __DIR__ . "/src/controllers/",
    "Helpers" => __DIR__ . "/src/helpers/",
    "Model" => __DIR__ . "/src/models/",
    "Decrypt" => __DIR__ . "/src/helpers/",
    "Middleware" => __DIR__ . "/src/middleware/",
    "" => __DIR__ . "/src/",
  ];

  $specialCases = [
    "Middleware\\ApiKeyMiddleware" =>
      __DIR__ . "/src/middleware/ValidateUserKeyMiddleware.php",
  ];

  if (isset($specialCases[$class])) {
    $file = $specialCases[$class];
    if (file_exists($file)) {
      require $file;
      return;
    } else {
      return;
    }
  }

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

}

spl_autoload_register("myAutoloader");
