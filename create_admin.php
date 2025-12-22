<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid; // If Ramsey is available, if not use DB UUID

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get Account/Business
    $accId = $pdo->query('SELECT id FROM accounts LIMIT 1')->fetchColumn();
    $busId = $pdo->query('SELECT id FROM businesses LIMIT 1')->fetchColumn();

    if (!$accId || !$busId) {
        die("No Account or Business found to attach user to.");
    }

    $email = 'admin@omnipos.test';

    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "User already exists.\n";
    } else {
        $pass = password_hash('secret', PASSWORD_BCRYPT);
        // Use UUID() from MySQL for ID
        $sql = "INSERT INTO users (id, account_id, business_id, name, email, password, phone, document_id, address, role) VALUES (UUID(), ?, ?, 'Test Admin', ?, ?, '555', 'DOC-TEST', 'Test Address', 'account_admin')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accId, $busId, $email, $pass]);
        echo "User created successfully.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
