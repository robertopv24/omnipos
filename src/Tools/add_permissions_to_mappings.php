<?php
// database/add_permissions_to_mappings.php
require __DIR__ . '/../../vendor/autoload.php';
use OmniPOS\Core\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

echo "<h1>Updating Route Mappings Schema</h1><pre>";

try {
    $pdo = Database::connect();
    
    // 1. Add Column if not exists
    try {
        $pdo->query("SELECT required_permission FROM route_mappings LIMIT 1");
        echo "Column 'required_permission' already exists.\n";
    } catch (Exception $e) {
        echo "Adding 'required_permission' column...\n";
        $pdo->exec("ALTER TABLE route_mappings ADD COLUMN required_permission VARCHAR(100) DEFAULT NULL AFTER access_level");
    }

    // 2. Map folders to permissions (Same logic as auto_discover)
    $defaults = [
        'account' => 'manage_settings',
        'admin' => 'manage_platform',
        'cash' => 'manage_cash',
        'clients' => 'view_sales',
        'finance' => 'view_finance',
        'inventory' => 'manage_inventory',
        'manufacture' => 'manage_manufacture',
        'platform' => 'manage_platform',
        'products' => 'manage_products',
        'purchases' => 'manage_purchases',
        'reports' => 'view_reports',
        'restoration' => 'view_restoration',
        'sales' => 'view_sales',
        'settings' => 'manage_settings',
        'shop' => 'manage_shop',
        'suppliers' => 'manage_purchases',
        'users' => 'manage_users'
    ];

    echo "Updating permissions based on target path...\n";
    foreach ($defaults as $folder => $perm) {
        $stmt = $pdo->prepare("UPDATE route_mappings SET required_permission = ? WHERE target_path LIKE ?");
        $stmt->execute([$perm, "$folder/%"]);
        echo "Updating $folder -> $perm: " . $stmt->rowCount() . " rows affected.\n";
    }

    echo "Done!";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";
