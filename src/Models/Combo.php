<?php

namespace OmniPOS\Models;

class Combo extends Model
{
    protected string $table = 'combos';

    /**
     * Obtiene los items de un combo por su product_id (el producto 'cabecera').
     */
    public function getItemsByProduct(string $productId): array
    {
        $pdo = \OmniPOS\Core\Database::connect();
        $sql = "SELECT ci.*, p.name, p.product_type 
                FROM combo_items ci
                JOIN combos c ON ci.combo_id = c.id
                JOIN products p ON ci.product_id = p.id
                WHERE c.product_id = :pid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll();
    }
}
