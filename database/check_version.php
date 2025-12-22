<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USERNAME'];
$pass = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    echo "Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
