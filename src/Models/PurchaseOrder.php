<?php

namespace OmniPOS\Models;

class PurchaseOrder extends Model
{
    protected string $table = 'purchase_orders';
    protected bool $isTenantScoped = true;

    /**
     * Obtener órdenes pendientes de recepción
     */
    public function getPending(): array
    {
        $sql = "SELECT po.*, s.name as supplier_name 
                FROM {$this->table} po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.business_id = :business_id 
                AND po.status IN ('pending', 'partial')
                ORDER BY po.created_at DESC";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['business_id' => \OmniPOS\Services\TenantService::getBusinessId()]);
        return $stmt->fetchAll();
    }
}
