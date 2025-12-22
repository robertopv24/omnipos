<?php

namespace OmniPOS\Models;

class Supplier extends Model
{
    protected string $table = 'suppliers';
    protected bool $isTenantScoped = true;

    /**
     * Obtener proveedores activos
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE business_id = :business_id AND is_active = 1 ORDER BY name ASC";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['business_id' => \OmniPOS\Services\TenantService::getBusinessId()]);
        return $stmt->fetchAll();
    }
}
