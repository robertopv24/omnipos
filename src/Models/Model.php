<?php

namespace OmniPOS\Models;

use OmniPOS\Core\Database;
use OmniPOS\Services\TenantService;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $isTenantScoped = true;

    protected function getPdo(): PDO
    {
        return Database::connect();
    }

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($this->isTenantScoped) {
            $businessId = TenantService::getBusinessId();
            $sql .= " WHERE business_id = :business_id";
            $params['business_id'] = $businessId;
        }

        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(string $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $params = ['id' => $id];

        if ($this->isTenantScoped) {
            $businessId = TenantService::getBusinessId();
            $sql .= " AND business_id = :business_id";
            $params['business_id'] = $businessId;
        }

        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        // Generar UUID si no existe
        if (!isset($data[$this->primaryKey])) {
            $data[$this->primaryKey] = $this->generateUuid();
        }

        if ($this->isTenantScoped && !isset($data['business_id'])) {
            $data['business_id'] = TenantService::getBusinessId();
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($data);
    }

    public function update(string $id, array $data): bool
    {
        $fields = "";
        foreach ($data as $key => $value) {
            if ($key === $this->primaryKey) continue;
            if ($this->isTenantScoped && $key === 'business_id') continue;
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;

        if ($this->isTenantScoped) {
            $businessId = TenantService::getBusinessId();
            $sql .= " AND business_id = :business_id";
            $data['business_id'] = $businessId;
        }

        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $params = ['id' => $id];

        if ($this->isTenantScoped) {
            $businessId = TenantService::getBusinessId();
            $sql .= " AND business_id = :business_id";
            $params['business_id'] = $businessId;
        }

        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    protected function generateUuid(): string
    {
        return $this->getPdo()->query("SELECT UUID()")->fetchColumn();
    }
}
