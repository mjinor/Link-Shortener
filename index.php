<?php

// Autoload files using the Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Loading dot env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

Predis\Autoloader::register();

try {
    \Mjinor\MjaShorturl\App\Bootstrap::boot();
} catch (Exception $e) {
    echo "Error : " . $e->getMessage();
    exit();
}

// Router file
require_once __DIR__ . '/src/Router.php';