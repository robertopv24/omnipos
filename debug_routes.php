<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use OmniPOS\Core\Session;
use OmniPOS\Core\Request;
use OmniPOS\Core\Response;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock Session for Super Admin
Session::set('user_id', '5cf2dbb1-a1be-11f0-9b3a-fbbcbd7f42d0');
Session::set('role', 'super_admin');
Session::set('account_id', '5cf2ae8b-a1be-11f0-9b3a-fbbcbd7f42d0');
Session::set('business_id', '5cf2b288-a1be-11f0-9b3a-fbbcbd7f42d0');

$routes = [
    '/finance/payroll' => 'OmniPOS\Controllers\FinanceController::payroll',
    '/manufacture/orders/create' => 'OmniPOS\Controllers\ManufactureController::createOrder',
    '/master-dashboard' => 'OmniPOS\Controllers\MasterDashboardController::index',
    '/platform/accounts' => 'OmniPOS\Controllers\PlatformAccountController::index'
];

foreach ($routes as $path => $action) {
    echo "--- Testing: $path ($action) ---\n";
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $path;
    
    $request = new Request();
    $response = new Response();
    
    try {
        list($controllerClass, $method) = explode('::', $action);
        $controller = new $controllerClass();
        $output = $controller->$method($request, $response);
        echo "SUCCESS: Output length " . strlen($output) . "\n";
    } catch (\Throwable $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        echo "In file: " . $e->getFile() . " line " . $e->getLine() . "\n";
    }
    echo "\n";
}
