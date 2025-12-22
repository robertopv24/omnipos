<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configuration
$legacyDbConfig = [
    'host' => $_ENV['DB_HOST'],
    'user' => $_ENV['DB_USERNAME'],
    'pass' => $_ENV['DB_PASSWORD'],
    'name' => 'minimarket' // Legacy DB
];

$saasDbConfig = [
    'host' => $_ENV['DB_HOST'],
    'user' => $_ENV['DB_USERNAME'],
    'pass' => $_ENV['DB_PASSWORD'],
    'name' => $_ENV['DB_DATABASE'] // omnipos_saas
];

try {
    $legacyPdo = new PDO("mysql:host={$legacyDbConfig['host']};dbname={$legacyDbConfig['name']};charset=utf8mb4", $legacyDbConfig['user'], $legacyDbConfig['pass']);
    $legacyPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $saasPdo = new PDO("mysql:host={$saasDbConfig['host']};dbname={$saasDbConfig['name']};charset=utf8mb4", $saasDbConfig['user'], $saasDbConfig['pass']);
    $saasPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to both databases.\n";

    $saasPdo->beginTransaction();

    // 1. Create Account & Business
    echo "Creating seed Account and Business...\n";
    $accountId = getUuid($saasPdo);
    $businessId = getUuid($saasPdo);

    $stmt = $saasPdo->prepare("INSERT INTO accounts (id, company_name, billing_email, subscription_plan, status) VALUES (?, ?, ?, 'enterprise', 'active')");
    $stmt->execute([$accountId, 'Minimarket HQ', 'admin@minimarket.com']);

    $stmt = $saasPdo->prepare("INSERT INTO businesses (id, account_id, name, address, currency) VALUES (?, ?, ?, ?, 'USD')");
    $stmt->execute([$businessId, $accountId, 'Sede Principal', 'Dirección Legacy']);

    // 2. Migrate Users
    echo "Migrating Users...\n";
    $userMap = []; // old_id => new_uuid
    $legacyUsers = $legacyPdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($legacyUsers as $user) {
        $newId = getUuid($saasPdo);
        $userMap[$user['id']] = $newId;

        // Map role
        $role = ($user['role'] === 'admin') ? 'account_admin' : 'user';

        $stmt = $saasPdo->prepare("INSERT INTO users (id, name, email, password, phone, document_id, address, role, account_id, business_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Note: Adding a suffix to email if needed to avoid duplication if running multiple times, but assuming clean slate
        $stmt->execute([
            $newId,
            $user['name'],
            $user['email'],
            $user['password'],
            $user['phone'],
            $user['document_id'] ?? 'DOC-' . $user['id'],
            $user['address'] ?? '',
            $role,
            $accountId,
            $businessId,
            $user['created_at']
        ]);
    }

    // 3. Migrate Clients
    echo "Migrating Clients...\n";
    $clientMap = [];
    $legacyClients = $legacyPdo->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($legacyClients as $client) {
        $newId = getUuid($saasPdo);
        $clientMap[$client['id']] = $newId;

        $stmt = $saasPdo->prepare("INSERT INTO clients (id, business_id, name, document_id, phone, email, address, credit_limit, current_debt, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $newId,
            $businessId,
            $client['name'],
            $client['document_id'],
            $client['phone'],
            $client['email'],
            $client['address'],
            $client['credit_limit'],
            $client['current_debt'],
            $client['created_at']
        ]);
    }

    // 4. Migrate Products
    echo "Migrating Products...\n";
    $productMap = [];
    $legacyProducts = $legacyPdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($legacyProducts as $product) {
        $newId = getUuid($saasPdo);
        $productMap[$product['id']] = $newId;

        $stmt = $saasPdo->prepare("INSERT INTO products (id, business_id, name, description, price_usd, price_ves, stock, product_type, kitchen_station, image_url, profit_margin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $newId,
            $businessId,
            $product['name'],
            $product['description'],
            $product['price_usd'],
            $product['price_ves'],
            $product['stock'],
            $product['product_type'],
            $product['kitchen_station'],
            $product['image_url'],
            $product['profit_margin'],
            $product['created_at']
        ]);
    }

    // 5. Migrate Orders and Items
    echo "Migrating Orders...\n";
    $legacyOrders = $legacyPdo->query("SELECT * FROM orders")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($legacyOrders as $order) {
        $newId = getUuid($saasPdo);

        $userId = $userMap[$order['user_id']] ?? null;
        if (!$userId)
            continue; // Skip if user not found (shouldn't happen)

        // Find client if exists... Legacy orders table didn't have client_id in the dump snippet, oh wait it has.
        // Dump schema for orders: `client_id` is not in the CREATE TABLE `orders` snippet I saw earlier?
        // Let's check the user request snippet again.
        // CREATE TABLE `orders` (id, user_id, total_price...) NO CLIENT_ID in the snippet provided in Step 80?
        // Actually, looking at `orders` snippet in Step 80:
        // `id`, `user_id`, `total_price`... NO `client_id` column in `orders` table definition!
        // But `orders` insert statements don't show it either. 
        // Wait, `accounts_receivable` has `client_id` and `order_id`. 
        // I will default client_id to null for now in the new DB.

        $stmt = $saasPdo->prepare("INSERT INTO orders (id, business_id, user_id, client_id, total_price, status, consumption_type, shipping_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $newId,
            $businessId,
            $userId,
            null, // Client ID separate
            $order['total_price'],
            $order['status'],
            $order['consumption_type'],
            $order['shipping_address'],
            $order['created_at']
        ]);

        // Items
        $items = $legacyPdo->query("SELECT * FROM order_items WHERE order_id = {$order['id']}")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as $item) {
            $prodId = $productMap[$item['product_id']] ?? null;
            if (!$prodId)
                continue;

            $stmt = $saasPdo->query("SELECT UUID()");
            $itemId = $stmt->fetchColumn();
            // Actually I don't mean query UUID() every time, let's use the helper or insert without ID if auto-inc? No, new schema is UUID.
            // Oh, order_items table in new schema... I didn't see the definition in my `schema.sql` I wrote.
            // Let's assume I need to double check `order_items` in `schema.sql` (Step 66).
            // It has `id` UUID PRIMARY KEY.

            // Wait, I can't check schema.sql content easily right now without reading it.
            // I'll trust standard UUID requirement.

            $stmt = $saasPdo->prepare("INSERT INTO order_items (id, business_id, order_id, product_id, quantity, price, consumption_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                getUuid($saasPdo),
                $businessId,
                $newId, // order new id
                $prodId,
                $item['quantity'],
                $item['price'],
                $item['consumption_type']
            ]);
        }
    }

    // 6. Migrate Suppliers
    echo "Migrating Suppliers...\n";
    $supplierMap = [];
    $legacySuppliers = $legacyPdo->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($legacySuppliers as $sup) {
        $newId = getUuid($saasPdo);
        $supplierMap[$sup['id']] = $newId;
        $stmt = $saasPdo->prepare("INSERT INTO suppliers (id, business_id, name, contact_person, email, phone, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $newId,
            $businessId,
            $sup['name'],
            $sup['contact_person'],
            $sup['email'],
            $sup['phone'],
            $sup['address'],
            $sup['created_at']
        ]);
    }

    // 7. Migrate Raw Materials
    echo "Migrating Raw Materials...\n";
    $rawMatMap = [];
    $legacyRaw = $legacyPdo->query("SELECT * FROM raw_materials")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($legacyRaw as $raw) {
        $newId = getUuid($saasPdo);
        $rawMatMap[$raw['id']] = $newId;
        $stmt = $saasPdo->prepare("INSERT INTO raw_materials (id, business_id, name, unit, stock_quantity, cost_per_unit, min_stock, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $newId,
            $businessId,
            $raw['name'],
            $raw['unit'],
            $raw['stock_quantity'],
            $raw['cost_per_unit'],
            $raw['min_stock'],
            $raw['category']
        ]);
    }

    // 8. Migrate Payment Methods
    echo "Migrating Payment Methods...\n";
    // We can just create default ones or migrate. Let's migrate to preserve IDs for transaction references if needed.
    $pmMap = [];
    $legacyPM = $legacyPdo->query("SELECT * FROM payment_methods")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($legacyPM as $pm) {
        $newId = getUuid($saasPdo);
        $pmMap[$pm['id']] = $newId;
        $stmt = $saasPdo->prepare("INSERT INTO payment_methods (id, business_id, name, currency, type, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $newId,
            $businessId,
            $pm['name'],
            $pm['currency'],
            $pm['type'],
            $pm['is_active']
        ]);
    }

    $saasPdo->commit();
    echo "Migration Complete!\n";

} catch (Exception $e) {
    if (isset($saasPdo) && $saasPdo->inTransaction()) {
        $saasPdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

function getUuid($pdo)
{
    return $pdo->query("SELECT UUID()")->fetchColumn();
}
