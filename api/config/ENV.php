<?php
namespace Loader;

use Dotenv\Dotenv;

class loadenv
{
    public static function initialize(string $directory): void
    {
        $dotenv = Dotenv::createImmutable($directory);
        $dotenv->load();
    }
}