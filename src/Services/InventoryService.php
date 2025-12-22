<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;

class InventoryService
{
    /**
     * Consume stock de un ítem (producto o materia prima) siguiendo FIFO.
     * Retorna el costo total de los ítems consumidos.
     */
    public function consumeStock(string $itemType, string $itemId, float $quantity, string $refType, ?string $refId): float
    {
        $pdo = Database::connect();
        $businessId = Session::get('business_id');
        $userId = Session::get('user_id');

        // 1. Buscar lotes disponibles ordenados por fecha de recepción (FIFO)
        $sql = "SELECT id, current_quantity, unit_cost FROM inventory_batches 
                WHERE business_id = :bid AND item_type = :type AND item_id = :iid 
                AND current_quantity > 0 
                ORDER BY received_at ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId, 'type' => $itemType, 'iid' => $itemId]);
        $batches = $stmt->fetchAll();

        $remaining = $quantity;
        $totalCost = 0;

        foreach ($batches as $batch) {
            if ($remaining <= 0)
                break;

            $toConsume = min($batch['current_quantity'], $remaining);
            $totalCost += $toConsume * $batch['unit_cost'];

            // 2. Descontar del lote
            $pdo->prepare("UPDATE inventory_batches SET current_quantity = current_quantity - :qty WHERE id = :id")
                ->execute(['qty' => $toConsume, 'id' => $batch['id']]);

            // 3. Registrar movimiento de trazabilidad
            $pdo->prepare("INSERT INTO inventory_movements (id, business_id, batch_id, type, quantity, reference_type, reference_id, created_by) 
                           VALUES (UUID(), :bid, :bid_id, 'exit', :qty, :ref_type, :ref_id, :uid)")
                ->execute([
                    'bid' => $businessId,
                    'bid_id' => $batch['id'],
                    'qty' => $toConsume,
                    'ref_type' => $refType,
                    'ref_id' => $refId,
                    'uid' => $userId
                ]);

            $remaining -= $toConsume;
        }

        if ($remaining > 0) {
            throw new \Exception("Stock insuficiente en lotes para completar el consumo de {$quantity} unidades.");
        }

        // 4. Actualizar Stock Maestro (Cache en tabla de productos/insumos)
        $tableName = ($itemType === 'product') ? 'products' : 'raw_materials';
        $pdo->prepare("UPDATE {$tableName} SET stock = stock - :qty WHERE id = :id")
            ->execute(['qty' => $quantity, 'id' => $itemId]);

        return $totalCost;
    }

    /**
     * Registra una nueva entrada de stock (crea un nuevo lote).
     */
    public function registerEntry(string $itemType, string $itemId, float $quantity, float $cost, ?string $batchNum = null, ?string $expiry = null): string
    {
        $pdo = Database::connect();
        $id = $pdo->query("SELECT UUID()")->fetchColumn();

        $sql = "INSERT INTO inventory_batches (id, business_id, item_type, item_id, batch_number, initial_quantity, current_quantity, unit_cost, expiry_date) 
                VALUES (:id, :bid, :type, :iid, :bnum, :qty, :qty, :cost, :expiry)";

        $pdo->prepare($sql)->execute([
            'id' => $id,
            'bid' => Session::get('business_id'),
            'type' => $itemType,
            'iid' => $itemId,
            'bnum' => $batchNum,
            'qty' => $quantity,
            'cost' => $cost,
            'expiry' => $expiry
        ]);

        return $id;
    }

    /**
     * Consume los ingredientes de una receta para un producto manufacturado (on-demand).
     */
    public function consumeRecipe(string $manufacturedProductId, float $quantity, ?string $refId): void
    {
        $pdo = Database::connect();
        
        // Cargar receta
        $sql = "SELECT raw_material_id, quantity_required FROM production_recipes 
                WHERE manufactured_product_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $manufacturedProductId]);
        $ingredients = $stmt->fetchAll();

        foreach ($ingredients as $ing) {
            $needed = $ing['quantity_required'] * $quantity;
            // Descontar materia prima siguiendo FIFO
            $this->consumeStock('raw_material', $ing['raw_material_id'], $needed, 'sale', $refId);
        }
    }

    /**
     * Realiza un ajuste manual de inventario.
     */
    public function adjustStock(string $itemType, string $itemId, float $quantity, string $adjustmentType, string $notes): void
    {
        $pdo = Database::connect();
        $businessId = Session::get('business_id');
        $userId = Session::get('user_id');

        if ($adjustmentType === 'entry') {
            // Un ajuste de entrada crea un nuevo lote (manual)
            // Usamos un costo referencial si es posible, o 0 si es ajuste puro
            $this->registerEntry($itemType, $itemId, $quantity, 0, 'AJUSTE-' . date('Ymd'), null);
            
            // Actualizar stock maestro
            $tableName = ($itemType === 'product') ? 'products' : 'raw_materials';
            $pdo->prepare("UPDATE {$tableName} SET stock = stock + :qty WHERE id = :id")
                ->execute(['qty' => $quantity, 'id' => $itemId]);
                
        } elseif ($adjustmentType === 'exit' || $adjustmentType === 'discard') {
            // Un ajuste de salida consume stock existente siguiendo FIFO
            $this->consumeStock($itemType, $itemId, $quantity, 'manual', null);
        }
    }
}
