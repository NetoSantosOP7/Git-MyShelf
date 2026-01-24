<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$path = dirname(__DIR__); 

if (file_exists($path . '/.env')) {
    $dotenv = Dotenv::createImmutable($path);
    $dotenv->safeLoad();
}

define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306');
define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');
define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '');
define('DB_CHARSET', 'utf8mb4');
