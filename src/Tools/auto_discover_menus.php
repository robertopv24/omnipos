<?php
// database/auto_discover_menus.php
require __DIR__ . '/../vendor/autoload.php';

use OmniPOS\Core\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "<h1>Syncing Views to Menus...</h1><pre>";

try {
    $pdo = Database::connect();
    $pdo->beginTransaction();

    // 1. Define Defaults Mappings
    $defaults = [
        'account' => ['icon' => 'fa fa-user-circle', 'perm' => 'manage_settings', 'title' => 'Mi Cuenta'],
        'admin' => ['icon' => 'fa fa-shield-alt', 'perm' => 'manage_platform', 'title' => 'Admin'],
        'auth' => ['skip' => true],
        'cash' => ['icon' => 'fa fa-money-bill-wave', 'perm' => 'manage_cash', 'title' => 'Caja'],
        'clients' => ['icon' => 'fa fa-users', 'perm' => 'view_sales', 'title' => 'Clientes'],
        'components' => ['skip' => true],
        'dashboard' => ['skip' => true], // Usually manually handled
        'finance' => ['icon' => 'fa fa-chart-line', 'perm' => 'view_finance', 'title' => 'Finanzas'],
        'home.php' => ['skip' => true],
        'inventory' => ['icon' => 'fa fa-boxes', 'perm' => 'manage_inventory', 'title' => 'Inventario'],
        'layouts' => ['skip' => true],
        'manufacture' => ['icon' => 'fa fa-industry', 'perm' => 'manage_manufacture', 'title' => 'Manufactura'],
        'pages' => ['skip' => true], // Static pages like about/terms
        'platform' => ['icon' => 'fa fa-server', 'perm' => 'manage_platform', 'title' => 'Plataforma'],
        'products' => ['icon' => 'fa fa-box-open', 'perm' => 'manage_products', 'title' => 'Productos'],
        'purchases' => ['icon' => 'fa fa-shopping-cart', 'perm' => 'manage_purchases', 'title' => 'Compras'],
        'reports' => ['icon' => 'fa fa-file-invoice-dollar', 'perm' => 'view_reports', 'title' => 'Reportes'],
        'restoration' => ['icon' => 'fa fa-utensils', 'perm' => 'view_restoration', 'title' => 'Restauración'],
        'sales' => ['icon' => 'fa fa-cash-register', 'perm' => 'view_sales', 'title' => 'Ventas'],
        'settings' => ['icon' => 'fa fa-cogs', 'perm' => 'manage_settings', 'title' => 'Configuración'],
        'shop' => ['icon' => 'fa fa-store', 'perm' => 'manage_shop', 'title' => 'Tienda'],
        'suppliers' => ['icon' => 'fa fa-truck', 'perm' => 'manage_purchases', 'title' => 'Proveedores'],
        'users' => ['icon' => 'fa fa-users-cog', 'perm' => 'manage_users', 'title' => 'Usuarios'],
        'widgets' => ['skip' => true],
    ];

    $viewsPath = __DIR__ . '/../src/Views';
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));

    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        if ($file->getExtension() !== 'php') continue;

        $relativePath = substr($file->getPathname(), strlen($viewsPath) + 1);
        $parts = explode('/', $relativePath);
        $topFolder = $parts[0];

        // Skip configuration
        if (($defaults[$topFolder]['skip'] ?? false)) continue;
        
        // Construct Route
        // Remove .php
        $routePath = '/' . str_replace('.php', '', $relativePath);
        // Replace 'index' with root of folder
        $routePath = str_replace('/index', '', $routePath);
        
        echo "Processing: $relativePath -> Path: $routePath ... ";

        // Check if exists in Menus
        $stmt = $pdo->prepare("SELECT id FROM menus WHERE url = ?");
        $stmt->execute([$routePath]);
        $menuExists = $stmt->fetch();

        // Check if Route Mapping exists
        $stmt = $pdo->prepare("SELECT id FROM route_mappings WHERE slug = ?");
        $stmt->execute([$routePath]);
        $mappingExists = $stmt->fetch();

        // Check if defined in routes.php (Simple check)
        $routesContent = file_get_contents(__DIR__ . '/../src/Config/routes.php');
        $isStaticRoute = strpos($routesContent, "'$routePath'") !== false;

        // 1. Ensure Route Mapping exists if not static
        if (!$isStaticRoute && !$mappingExists) {
             echo " [MAPPING] ";
             $targetPath = str_replace('.php', '', $relativePath); // e.g. clients/create
             $pdo->prepare("INSERT INTO route_mappings (id, slug, target_path, access_level) VALUES (UUID(), ?, ?, 'auth')")
                 ->execute([$routePath, $targetPath]);
        }

        if ($menuExists) {
            echo "Menu Exists. Skipping.\n";
            continue;
        }

        // infer properties
        $folderConfig = $defaults[$topFolder] ?? ['icon' => 'fa fa-circle', 'perm' => null, 'title' => ucfirst($topFolder)];
        
        // Determine Title from filename
        $basename = basename($file->getFilename(), '.php');
        $title = ucfirst(str_replace('_', ' ', $basename));
        if ($basename === 'index') $title = "Listado de " . $folderConfig['title'];

        // Determine Parent Group ID
        // Try to find a sidebar group with the top folder title
        $stmt = $pdo->prepare("SELECT id FROM menus WHERE title = ? AND parent_id IS NULL AND type = 'sidebar'");
        $stmt->execute([$folderConfig['title']]);
        $parentId = $stmt->fetchColumn();

        // If Parent doesn't exist, create it?
        // Ideally we assume parents exist from fix_menus_permissions.php
        // If not, we could default to 'Configuración' or create new
        if (!$parentId) {
            echo "[NEW PARENT] ";
            $parentId = $pdo->query("SELECT UUID()")->fetchColumn();
            $pdo->prepare("INSERT INTO menus (id, title, url, icon, position, type, visibility) VALUES (?, ?, '#', ?, 50, 'sidebar', 'private')")
                ->execute([$parentId, $folderConfig['title'], $folderConfig['icon']]);
        }

        // Get Permission ID
        $permId = null;
        if ($folderConfig['perm']) {
            $stmt = $pdo->prepare("SELECT id FROM permissions WHERE name = ?");
            $stmt->execute([$folderConfig['perm']]);
            $permId = $stmt->fetchColumn();
        }

        echo "Inserting [$title] into [{$folderConfig['title']}]... ";
        
        $pdo->prepare("INSERT INTO menus (id, parent_id, title, url, position, type, visibility, required_permission_id) 
                       VALUES (UUID(), ?, ?, ?, 99, 'sidebar', 'private', ?)")
            ->execute([$parentId, $title, $routePath, $permId]);
            
        echo "DONE.\n";
    }

    $pdo->commit();
    echo "\nSync Completed Successfully!";

} catch (Exception $e) {
    if (isset($pdo)) $pdo->rollBack();
    echo "\nError: " . $e->getMessage();
}
echo "</pre>";
