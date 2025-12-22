<?php
// debug_router_advanced.php
require __DIR__ . '/../vendor/autoload.php';

use OmniPOS\Core\App;
use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Router;

// Mock environment
$_SERVER['REQUEST_METHOD'] = 'GET';
// Test the problematic URL
$_SERVER['REQUEST_URI'] = '/omnipos/platform/languages';
$_SERVER['SCRIPT_NAME'] = '/omnipos/public/index.php'; // Standard rewrite path

$request = new Request();
echo "<h1>Router Debugger</h1>";
echo "<pre>";
echo "Raw URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Detected Path (Internal): [" . $request->getPath() . "]\n";
echo "Detected Method: " . $request->getMethod() . "\n";

// Load Routes
$app = new App();
$router = $app->getRouter();
require __DIR__ . '/../src/Config/routes.php';

// Inspect Router Object reflection
$ref = new ReflectionClass($router);
$props = $ref->getProperty('routes');
$props->setAccessible(true);
$routes = $props->getValue($router);

echo "\nRegistered Routes (GET):\n";
$found = false;
foreach ($routes['get'] as $path => $conf) {
    if ($path === '/platform/languages') {
        echo " [MATCH FOUND] $path -> " . print_r($conf['callback'], true) . "\n";
        $found = true;
    } else {
        // echo " $path\n";
    }
}

if (!$found) {
    echo "\n[ERROR] Route /platform/languages NOT found in registry.\n";
} else {
    echo "\n[SUCCESS] Route is registered.\n";
}

// simulate resolve
echo "\n--- Resolving ---\n";
// logic copied from Router::resolve for testing
$path = $request->getPath();
$method = 'get';
$exactRoute = $routes[$method][$path] ?? false;

if ($exactRoute) {
    echo "Exact Match Found!\n";
} else {
    echo "Exact Match FAILED.\n";
    // Check regex
    echo "Checking Regex...\n";
    foreach ($routes[$method] as $routePath => $routeConfig) {
         if (strpos($routePath, '{') === false) continue;
         $pattern = "#^" . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath) . "$#";
         echo " Testing pattern $pattern against $path... ";
         if (preg_match($pattern, $path)) {
             echo "MATCH!\n";
         } else {
             echo "no.\n";
         }
    }
}
echo "</pre>";
