<?php

function myAutoloader($class)
{
    // Define base directories for namespaces
    $baseDirs = [
        "DB" => __DIR__ . "/config/",
        "Controller" => __DIR__ . "/src/controllers/",
        "Helpers" => __DIR__ . "/src/helpers/",
        "Model" => __DIR__ . "/src/models/",
        "Decrypt" => __DIR__ . "/src/helpers/",
        "Middleware" => __DIR__ . "/src/middleware/",
        "" => __DIR__ . "/src/", // Default directory for unmatched namespaces
    ];

    // Special cases for specific classes
    $specialCases = [
        "Middleware\\ApiKeyMiddleware" => __DIR__ . "/src/middleware/ValidateUserKeyMiddleware.php",
    ];

    // Check if the class is a special case
    if (isset($specialCases[$class])) {
        $file = $specialCases[$class];
        if (file_exists($file)) {
            require $file;
            return;
        }
        // If the special case file doesn't exist, let the autoloader continue searching
    }

    // Extract the namespace prefix from the class
    $namespacePrefix = strtok($class, '\\') . '\\';

    // Check if the namespace prefix matches any base directory
    if (isset($baseDirs[$namespacePrefix])) {
        $dir = $baseDirs[$namespacePrefix];
        $relativeClass = substr($class, strlen($namespacePrefix));
        $file = $dir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }

    // Fallback for unmatched namespaces
    $file = $baseDirs[''] . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require $file;
        return;
    }

    // Optionally, log or throw an error if the class is not found
    throw new Exception("Class $class could not be autoloaded.");
}

// Register the autoloader
spl_autoload_register("myAutoloader");