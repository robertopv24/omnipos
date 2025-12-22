<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * OmniPOS - Front Controller
 */

// Define application start time
define('APP_START', microtime(true));

// Load Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load Environment Variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = new OmniPOS\Core\App();

// Routes
require_once __DIR__ . '/../src/Config/routes.php';

$app->run();
