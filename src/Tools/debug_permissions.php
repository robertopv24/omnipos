<?php
// debug_permissions.php
require __DIR__ . '/../vendor/autoload.php';

use OmniPOS\Core\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "<h1>Role Permissions Check</h1>";
echo "<pre>";

try {
    $pdo = Database::connect();

    // Check which roles have 'manage_platform'
    echo "Roles with 'manage_platform':\n";
    $sql = "SELECT r.name, r.id 
            FROM roles r
            JOIN role_permissions rp ON r.id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE p.name = 'manage_platform'";
            
    $stmt = $pdo->query($sql);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($roles)) {
        echo "[WARNING] NO ROLE has 'manage_platform' permission!\n";
    } else {
        foreach ($roles as $r) {
            echo " - {$r['name']} (ID: {$r['id']})\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";
