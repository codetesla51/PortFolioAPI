<?php
function myAutoloader($class)
{
    // Define base directories with more robust path handling
    $baseDirs = [
        "DB" => __DIR__ . DIRECTORY_SEPARATOR . "config",
        "Controller" => __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "controllers",
        "Helpers" => __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "helpers",
        "Model" => __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "models",
        "Decrypt" => __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "helpers",
        "Middleware" => __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "middleware",
        "" => __DIR__ . DIRECTORY_SEPARATOR . "src",
    ];

    // Special cases with precise namespace mapping
    $specialCases = [
        "Middleware\\ApiKeyMiddleware" => __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "middleware" . DIRECTORY_SEPARATOR . "ValidateUserKeyMiddleware.php",
    ];

    // Handle special cases first with early return
    if (isset($specialCases[$class])) {
        $file = $specialCases[$class];
        return file_exists($file) ? require $file : false;
    }

    // Normalize class name for matching
    $normalizedClass = str_replace("\\", DIRECTORY_SEPARATOR, $class);

    // Iterate through base directories
    foreach ($baseDirs as $namespace => $dir) {
        // Check if the class belongs to this namespace
        if ($namespace === "" || strpos($class, $namespace . "\\") === 0) {
            // Remove namespace prefix
            $relativeClass = $namespace === "" 
                ? $normalizedClass 
                : substr($normalizedClass, strlen($namespace . "\\"));

            // Construct full file path
            $file = $dir . DIRECTORY_SEPARATOR . $relativeClass . ".php";

            // Attempt to load file
            if (file_exists($file)) {
                require $file;
                return true;
            }
        }
    }

    // Optional: Log or handle cases where file is not found
    return false;
}

// Register the autoloader
spl_autoload_register("myAutoloader");