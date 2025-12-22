<?php

namespace OmniPOS\Models;

class ManufacturedProduct extends Model
{
    protected string $table = 'manufactured_products';
    protected bool $isTenantScoped = true;

    public function getRecipe(string $productId): array
    {
        $sql = "SELECT r.*, rm.name as material_name, rm.unit 
                FROM production_recipes r
                JOIN raw_materials rm ON r.raw_material_id = rm.id
                WHERE r.manufactured_product_id = :id";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }
}
