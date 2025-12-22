<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USERNAME'];
$pass = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_DATABASE'];

echo "Connecting to MySQL server...\n";

try {
    // Connect without DB selected to create it
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Creating database '$dbName' if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Select the DB
    $pdo->exec("USE `$dbName`");

    echo "Importing schema from schema.sql...\n";
    $sql = file_get_contents(__DIR__ . '/schema.sql');

    // Execute raw SQL - split by ; might be needed for some drivers but PDO can handle multi-query if configured or supported.
    // However, schema.sql has DELIMITER or complex statements? The provided one looks standard.
    // Let's try executing it directly.
    $pdo->exec($sql);

    echo "Database schema imported successfully!\n";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
    exit(1);
}
