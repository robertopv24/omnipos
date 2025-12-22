<?php

namespace OmniPOS\Models;

class Table extends Model
{
    protected string $table = 'restoration_tables';
    
    /**
     * Obtiene mesas activas por negocio.
     */
    public function getByBusiness(string $businessId): array
    {
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM restoration_tables WHERE business_id = :bid AND is_active = 1 ORDER BY table_number ASC");
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll();
    }
}
