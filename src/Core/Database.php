<?php

namespace OmniPOS\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'];
            $db = $_ENV['DB_DATABASE'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];
            $port = $_ENV['DB_PORT'] ?? 3306;
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // In production, log this error and show generic message
                die("Database Connection Failed: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
