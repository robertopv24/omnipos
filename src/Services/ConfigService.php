<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use PDO;

class ConfigService
{
    protected array $config = [];
    protected static ?ConfigService $instance = null;

    public function __construct()
    {
        $this->loadConfig();
    }

    public static function getInstance(): ConfigService
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function loadConfig(): void
    {
        $pdo = Database::connect();
        try {
            // Load in order of priority: Global -> Account -> Business
            // Last one wins in the associative array merge
            $accountId = \OmniPOS\Core\Session::get('account_id');
            $businessId = \OmniPOS\Core\Session::get('business_id');

            $sql = "SELECT config_key, config_value, account_id, business_id FROM global_config 
                    WHERE (account_id IS NULL AND business_id IS NULL)";
            
            $params = [];
            if ($accountId) {
                $sql .= " OR (account_id = :aid AND business_id IS NULL)";
                $params['aid'] = $accountId;
            }
            if ($businessId) {
                $sql .= " OR (business_id = :bid)";
                $params['bid'] = $businessId;
            }

            // We sort by NULLs last to ensure Business/Account overrides come after Global during fetch
            // (or we can just fetch and merge in PHP which is safer for logic)
            $sql .= " ORDER BY account_id ASC, business_id ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->config[$row['config_key']] = $row['config_value'];
            }
        } catch (\PDOException $e) {
            // Log error
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->config;
    }
}
