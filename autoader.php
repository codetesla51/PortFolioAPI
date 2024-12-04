<?php
function myAutoloader($class)
{
  $baseDir = __DIR__ . "/config/";
  $class = str_replace("DB\\", "", $class);
  $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

  $file = $baseDir . $class . ".php";
  if (file_exists($file)) {
    require $file;
  } else {
    echo "File not found: $file\n";
  }
}

spl_autoload_register("myAutoloader");
