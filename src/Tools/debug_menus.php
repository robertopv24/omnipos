<?php
require __DIR__ . '/../vendor/autoload.php';

use OmniPOS\Core\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "<h1>Analysis of Menus and Permissions</h1>";
echo "<pre>";

try {
    $pdo = Database::connect();
    
    // 1. Get All Menus with their permission names
    $sql = "SELECT m.id, m.title, m.url, m.type, p.name as required_permission, m.visibility 
            FROM menus m 
            LEFT JOIN permissions p ON m.required_permission_id = p.id 
            ORDER BY m.type, m.position";
            
    $stmt = $pdo->query($sql);
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo str_pad("TITLE", 30) . str_pad("URL", 30) . str_pad("PERMISSION", 30) . "VISIBILITY\n";
    echo str_repeat("-", 110) . "\n";
    
    foreach ($menus as $menu) {
        echo str_pad(substr($menu['title'], 0, 28), 30);
        echo str_pad(substr($menu['url'], 0, 28), 30);
        echo str_pad($menu['required_permission'] ?? 'NULL', 30);
        echo $menu['visibility'] . "\n";
    }

    // 2. Check specific Platform permissions
    echo "\n\nChecking 'manage_platform' permission existence:\n";
    $perm = $pdo->query("SELECT * FROM permissions WHERE name = 'manage_platform'")->fetch();
    if ($perm) {
        echo "[OK] Permission 'manage_platform' exists (ID: {$perm['id']}).\n";
    } else {
        echo "[WARNING] Permission 'manage_platform' DOES NOT EXIST.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";
