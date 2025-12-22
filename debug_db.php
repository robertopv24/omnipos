<?php
require_once __DIR__ . '/vendor/autoload.php';

use OmniPOS\Core\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::connect();
    
    echo "--- Schema for 'clients' ---\n";
    $stmt = $pdo->query("DESCRIBE clients");
    while ($row = $stmt->fetch()) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }

    echo "\n--- Schema for 'products' ---\n";
    $stmt = $pdo->query("DESCRIBE products");
    while ($row = $stmt->fetch()) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
