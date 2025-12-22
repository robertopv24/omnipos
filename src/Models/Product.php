<?php

namespace OmniPOS\Models;

class Product extends Model
{
    protected string $table = 'products';
    protected bool $isTenantScoped = true;

    /**
     * Obtener productos con stock bajo el mínimo
     */
    public function getLowStock(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE stock < min_stock AND business_id = :business_id";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['business_id' => \OmniPOS\Services\TenantService::getBusinessId()]);
        return $stmt->fetchAll();
    }
}
