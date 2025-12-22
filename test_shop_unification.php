<?php
require_once __DIR__ . '/vendor/autoload.php';

use OmniPOS\Core\Session;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Mock Session
Session::set('business_id', '5cf2b288-a1be-11f0-9b3a-fbbcbd7f42d0');

$pdo = \OmniPOS\Core\Database::connect();
$businessId = Session::get('business_id');

// Test 1: Limit 25
$limit = 25;
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE business_id = ? AND category_type = 'resale'");
$stmt->execute([$businessId]);
$total = $stmt->fetchColumn();
echo "Total products in DB: $total\n";

// Test 2: Search by SKU (assuming we know one or just testing query)
$query = "ABC"; // Mock SKU or name part
$searchParam = "%$query%";
$countSql = "SELECT COUNT(*) FROM products 
            WHERE business_id = ? 
            AND (name LIKE ? OR description LIKE ? OR sku LIKE ?) 
            AND category_type = 'resale'";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute([$businessId, $searchParam, $searchParam, $searchParam]);
echo "Search results for '$query': " . $countStmt->fetchColumn() . "\n";

// Test 3: Pagination with limit 25
$sql = "SELECT * FROM products WHERE business_id = ? AND category_type = 'resale' LIMIT 25 OFFSET 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([$businessId]);
$products = $stmt->fetchAll();
echo "Products on page 1: " . count($products) . "\n";
?>
