<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Models\ManufacturedProduct;
use OmniPOS\Models\ProductionOrder;
use OmniPOS\Core\Session;

class ProductionService
{
    public function processOrder(string $manufacturedProductId, float $quantity, string $userId): array
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $model = new ManufacturedProduct();
            $manufactured = $model->find($manufacturedProductId);

            // 1. Obtener receta
            $sql = "SELECT r.*, rm.stock_quantity as current_stock, rm.name, rm.cost_per_unit
                    FROM production_recipes r
                    JOIN raw_materials rm ON r.raw_material_id = rm.id
                    WHERE r.manufactured_product_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $manufacturedProductId]);
            $ingredients = $stmt->fetchAll();

            $totalCost = 0;

            // 2. Descontar materias primas usando FIFO y calcular costo total
            $inventoryService = new \OmniPOS\Services\InventoryService();
            foreach ($ingredients as $ing) {
                $needed = $ing['quantity_required'] * $quantity;
                // The consumeStock method should handle stock validation internally.
                // We pass null for orderId initially, it will be updated later if needed.
                $consumedCost = $inventoryService->consumeStock('raw_material', $ing['raw_material_id'], $needed, 'production', null);
                if ($consumedCost === false) {
                    throw new \Exception("Stock insuficiente de: " . $ing['name']);
                }
                $totalCost += $consumedCost;
            }

            // 3. Crear orden de producción
            $order = new ProductionOrder();
            $order->create([
                'business_id' => Session::get('business_id'),
                'manufactured_product_id' => $manufacturedProductId,
                'quantity_produced' => $quantity,
                'total_cost' => $totalCost,
                'created_by' => $userId
            ]);

            // 4. Actualizar ManufacturedProduct (stock y costo promedio)
            $newStock = $manufactured['stock'] + $quantity;
            $currentTotalValue = $manufactured['stock'] * $manufactured['unit_cost_average'];
            $newAvgCost = ($currentTotalValue + $totalCost) / $newStock;

            $pdo->prepare("UPDATE manufactured_products SET stock = :stock, unit_cost_average = :cost, last_production_date = NOW() WHERE id = :id")
                ->execute([
                    'stock' => $newStock,
                    'cost' => $newAvgCost,
                    'id' => $manufacturedProductId
                ]);

            $pdo->commit();
            return ['success' => true];

        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
