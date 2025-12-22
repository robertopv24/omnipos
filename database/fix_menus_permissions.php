<?php

require __DIR__ . '/../vendor/autoload.php';

use OmniPOS\Core\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "Starting Menu and Permissions fix...\n";

try {
    $pdo = Database::connect();
    $pdo->beginTransaction();

    // 1. Ensure Roles exist
    echo "Checking roles...\n";
    $roles = [
        ['name' => 'super_admin', 'description' => 'Acceso Total al Sistema'],
        ['name' => 'account_admin', 'description' => 'Administrador de Cuenta'],
        ['name' => 'admin', 'description' => 'Administrador de Negocio'],
        ['name' => 'manager', 'description' => 'Gerente de Negocio'],
        ['name' => 'cashier', 'description' => 'Cajero / Punto de Venta'],
        ['name' => 'user', 'description' => 'Usuario Estándar']
    ];

    foreach ($roles as $r) {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute([$r['name']]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO roles (id, name, description, is_system_role, account_id) VALUES (UUID(), ?, ?, 1, (SELECT id FROM accounts LIMIT 1))")
                ->execute([$r['name'], $r['description']]);
        }
    }

    // 2. Add Permissions
    echo "Adding permissions...\n";
    $permissions = [
        ['name' => 'view_dashboard', 'desc' => 'Ver el panel principal', 'res' => 'dashboard', 'act' => 'read'],
        ['name' => 'manage_products', 'desc' => 'Gestionar productos', 'res' => 'products', 'act' => 'manage'],
        ['name' => 'view_sales', 'desc' => 'Ver ventas', 'res' => 'sales', 'act' => 'read'],
        ['name' => 'process_sales', 'desc' => 'Procesar ventas (POS)', 'res' => 'sales', 'act' => 'create'],
        ['name' => 'manage_inventory', 'desc' => 'Gestionar inventario', 'res' => 'inventory', 'act' => 'manage'],
        ['name' => 'manage_purchases', 'desc' => 'Gestionar compras', 'res' => 'purchases', 'act' => 'manage'],
        ['name' => 'manage_finance', 'desc' => 'Gestionar finanzas y caja', 'res' => 'finance', 'act' => 'manage'],
        ['name' => 'view_reports', 'desc' => 'Ver reportes', 'res' => 'reports', 'act' => 'read'],
        ['name' => 'manage_users', 'desc' => 'Gestionar usuarios', 'res' => 'users', 'act' => 'manage'],
        ['name' => 'manage_settings', 'desc' => 'Gestionar configuración', 'res' => 'settings', 'act' => 'manage'],
        ['name' => 'manage_restoration', 'desc' => 'Gestionar restauración', 'res' => 'restoration', 'act' => 'manage'],
        ['name' => 'manage_manufacture', 'desc' => 'Gestionar manufactura/recetas', 'res' => 'manufacture', 'act' => 'manage'],
        ['name' => 'manage_shop', 'desc' => 'Gestionar tienda/e-commerce', 'res' => 'shop', 'act' => 'manage'],
        ['name' => 'manage_platform', 'desc' => 'Gestionar Plataforma (SaaS)', 'res' => 'platform', 'act' => 'manage']
    ];

    foreach ($permissions as $p) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO permissions (id, name, description, resource, action) VALUES (UUID(), ?, ?, ?, ?)");
        $stmt->execute([$p['name'], $p['desc'], $p['res'], $p['act']]);
    }

    // 3. Link Permissions to Roles
    echo "Resetting role permissions...\n";
    $pdo->exec("DELETE FROM role_permissions");

    echo "Linking permissions to admin roles...\n";
    
    // Regular Admins get business permissions
    $businessAdmins = ['account_admin', 'admin'];
    foreach ($businessAdmins as $roleName) {
        $pdo->exec("INSERT INTO role_permissions (role_id, permission_id) 
                    SELECT r.id, p.id FROM roles r CROSS JOIN permissions p 
                    WHERE r.name = '$roleName' AND p.name != 'manage_platform'");
    }

    // Super Admin gets EVERYTHING including manage_platform
    $pdo->exec("INSERT INTO role_permissions (role_id, permission_id) 
                SELECT r.id, p.id FROM roles r CROSS JOIN permissions p 
                WHERE r.name = 'super_admin'");


    // 4. Create Sidebar Menus
    echo "Cleaning old sidebar menus...\n";
    $pdo->exec("DELETE FROM menus WHERE type = 'sidebar'");

    echo "Creating sidebar menu structure...\n";
    
    // Parent Menus
    $menuGroups = [
        ['title' => 'Tablero', 'url' => '/dashboard', 'icon' => 'fa fa-th-large', 'pos' => 1, 'perm' => 'view_dashboard'],
        ['title' => 'Ventas', 'url' => '#', 'icon' => 'fa fa-shopping-cart', 'pos' => 2, 'perm' => 'view_sales'],
        ['title' => 'Tienda Online', 'url' => '#', 'icon' => 'fa fa-globe', 'pos' => 3, 'perm' => 'manage_shop'],
        ['title' => 'Productos', 'url' => '#', 'icon' => 'fa fa-box', 'pos' => 4, 'perm' => 'manage_products'],
        ['title' => 'Inventario', 'url' => '#', 'icon' => 'fa fa-warehouse', 'pos' => 5, 'perm' => 'manage_inventory'],
        ['title' => 'Compras', 'url' => '#', 'icon' => 'fa fa-truck', 'pos' => 6, 'perm' => 'manage_purchases'],
        ['title' => 'Finanzas', 'url' => '#', 'icon' => 'fa fa-wallet', 'pos' => 7, 'perm' => 'manage_finance'],
        ['title' => 'Restauración', 'url' => '#', 'icon' => 'fa fa-utensils', 'pos' => 8, 'perm' => 'manage_restoration'],
        ['title' => 'Configuración', 'url' => '#', 'icon' => 'fa fa-cogs', 'pos' => 9, 'perm' => 'manage_settings']
    ];

    $groupIds = [];
    foreach ($menuGroups as $m) {
        $id = $pdo->query("SELECT UUID()")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO menus (id, title, url, icon, position, type, visibility, required_permission_id) 
                                VALUES (?, ?, ?, ?, ?, 'sidebar', 'private', (SELECT id FROM permissions WHERE name = ?))");
        $stmt->execute([$id, $m['title'], $m['url'], $m['icon'], $m['pos'], $m['perm']]);
        $groupIds[$m['title']] = $id;
    }

    // Submenus
    $submenus = [
        // Ventas
        ['group' => 'Ventas', 'title' => 'Punto de Venta', 'url' => '/sales/pos', 'pos' => 1],
        ['group' => 'Ventas', 'title' => 'Listado de Ventas', 'url' => '/sales', 'pos' => 2],
        
        // Tienda Online
        ['group' => 'Tienda Online', 'title' => 'Catálogo', 'url' => '/shop', 'pos' => 1],
        ['group' => 'Tienda Online', 'title' => 'Carrito', 'url' => '/shop/cart', 'pos' => 2],
        
        // Productos
        ['group' => 'Productos', 'title' => 'Gestión de Productos', 'url' => '/products', 'pos' => 1],
        ['group' => 'Productos', 'title' => 'Recetas', 'url' => '/manufacture/recipes', 'pos' => 2],
        ['group' => 'Productos', 'title' => 'Producción', 'url' => '/manufacture/orders', 'pos' => 3],
        
        // Inventario
        ['group' => 'Inventario', 'title' => 'Ajustes', 'url' => '/inventory/adjust', 'pos' => 1],
        ['group' => 'Inventario', 'title' => 'Trazabilidad', 'url' => '/inventory/traceability', 'pos' => 2],
        
        // Compras
        ['group' => 'Compras', 'title' => 'Órdenes de Compra', 'url' => '/purchases', 'pos' => 1],
        ['group' => 'Compras', 'title' => 'Proveedores', 'url' => '/suppliers', 'pos' => 2],
        ['group' => 'Compras', 'title' => 'Recepción', 'url' => '/purchases/receive', 'pos' => 3],
        
        // Tablero
        ['group' => 'Tablero', 'title' => 'Indicadores', 'url' => '/dashboard', 'pos' => 1],
        ['group' => 'Tablero', 'title' => 'Rentabilidad', 'url' => '/reports/profitability', 'pos' => 2],
        ['group' => 'Tablero', 'title' => 'Master Dashboard', 'url' => '/master-dashboard', 'pos' => 3],

        // Finanzas
        ['group' => 'Finanzas', 'title' => 'Arqueo de Caja', 'url' => '/cash', 'pos' => 1],
        ['group' => 'Finanzas', 'title' => 'Caja Chica', 'url' => '/finance/petty-cash', 'pos' => 2],
        ['group' => 'Finanzas', 'title' => 'Cuentas por Cobrar', 'url' => '/finance/cxc', 'pos' => 3],
        ['group' => 'Finanzas', 'title' => 'Cuentas por Pagar', 'url' => '/finance/cxp', 'pos' => 4],
        ['group' => 'Finanzas', 'title' => 'Nómina', 'url' => '/finance/payroll', 'pos' => 5],
        ['group' => 'Finanzas', 'title' => 'Libro Contable', 'url' => '/finance/ledger', 'pos' => 6],
        
        // Restauración
        ['group' => 'Restauración', 'title' => 'Gestión de Mesas', 'url' => '/restoration/tables', 'pos' => 1],
        ['group' => 'Restauración', 'title' => 'KDS (Cocina)', 'url' => '/restoration/kds', 'pos' => 2],
        ['group' => 'Restauración', 'title' => 'Menú Digital', 'url' => '/restoration/digital_menu', 'pos' => 3],
        
        // Configuración
        ['group' => 'Configuración', 'title' => 'Métodos de Pago', 'url' => '/settings/payment_methods', 'pos' => 1],
        ['group' => 'Configuración', 'title' => 'Impuestos', 'url' => '/admin/tax_rates', 'pos' => 2],
        ['group' => 'Configuración', 'title' => 'Gestión de Usuarios', 'url' => '/users', 'pos' => 3],
        ['group' => 'Configuración', 'title' => 'Mi Negocio', 'url' => '/account/businesses', 'pos' => 4],

        // Gestión SaaS (Only for Super Admin)
        ['group' => 'Gestión SaaS', 'title' => 'Tablero Global', 'url' => '/platform/dashboard', 'pos' => 1],
        ['group' => 'Gestión SaaS', 'title' => 'Cuentas SaaS', 'url' => '/platform/accounts', 'pos' => 2],
        ['group' => 'Gestión SaaS', 'title' => 'Planes de Suscripción', 'url' => '/platform/plans', 'pos' => 3],
        ['group' => 'Gestión SaaS', 'title' => 'Traducciones / Idiomas', 'url' => '/platform/languages', 'pos' => 4],
        ['group' => 'Gestión SaaS', 'title' => 'Publicidad y Avisos', 'url' => '/platform/ads', 'pos' => 5],
        ['group' => 'Gestión SaaS', 'title' => 'Configuración Global', 'url' => '/platform/settings', 'pos' => 6],
        ['group' => 'Gestión SaaS', 'title' => 'Editor de Menús', 'url' => '/platform/menus', 'pos' => 7]
    ];

    // Create SaaS Group explicitly with private visibility and manage_platform permission
    $saasId = $pdo->query("SELECT UUID()")->fetchColumn();
    $pdo->prepare("INSERT INTO menus (id, title, url, icon, position, type, visibility, required_permission_id) 
                    VALUES (?, 'Gestión SaaS', '#', 'fa fa-server', 10, 'sidebar', 'private', (SELECT id FROM permissions WHERE name = 'manage_platform'))")
        ->execute([$saasId]);
    $groupIds['Gestión SaaS'] = $saasId;

    foreach ($submenus as $s) {
        $parentId = $groupIds[$s['group']];
        $stmt = $pdo->prepare("INSERT INTO menus (id, parent_id, title, url, position, type, visibility) 
                                VALUES (UUID(), ?, ?, ?, ?, 'sidebar', 'private')");
        $stmt->execute([$parentId, $s['title'], $s['url'], $s['pos']]);
    }

    $pdo->commit();
    echo "Menu and Permissions fix completed successfully!\n";

} catch (Exception $e) {
    if (isset($pdo)) $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
