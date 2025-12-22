<?php
require __DIR__ . '/../vendor/autoload.php';

use OmniPOS\Core\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "Running Migration: Builder Tables...\n";

try {
    $pdo = Database::connect();
    $sql = file_get_contents(__DIR__ . '/migrations/20251221_create_builder_tables.sql');
    
    $pdo->exec($sql);
    echo "Migration successful!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
