<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use OmniPOS\Models\PurchaseOrder;
use PDO;

class PurchaseService
{
    /**
     * Crea una nueva orden de compra
     */
    public function createOrder(array $data): array
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $businessId = Session::get('business_id');
            $userId = Session::get('user_id');

            // Generar número de orden
            $orderNumber = $this->generateOrderNumber($businessId);

            // Calcular total
            $totalCost = 0;
            foreach ($data['items'] as $item) {
                $totalCost += $item['quantity'] * $item['unit_cost'];
            }

            // Crear orden
            $orderId = $pdo->query("SELECT UUID()")->fetchColumn();
            $sql = "INSERT INTO purchase_orders (id, business_id, supplier_id, order_number, total_cost, payment_term_days, notes, created_by)
                    VALUES (:id, :bid, :sid, :onum, :total, :terms, :notes, :uid)";
            
            $pdo->prepare($sql)->execute([
                'id' => $orderId,
                'bid' => $businessId,
                'sid' => $data['supplier_id'],
                'onum' => $orderNumber,
                'total' => $totalCost,
                'terms' => $data['payment_term_days'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'uid' => $userId
            ]);

            // Crear items de la orden
            foreach ($data['items'] as $item) {
                $itemId = $pdo->query("SELECT UUID()")->fetchColumn();
                
                // Normalizar item_type: product_resale y product_operational se guardan como 'product'
                $itemType = $item['item_type'];
                if ($itemType === 'product_resale' || $itemType === 'product_operational') {
                    $itemType = 'product';
                }
                
                $sql = "INSERT INTO purchase_order_items (id, purchase_order_id, item_type, item_id, quantity, unit_cost, batch_number, expiry_date)
                        VALUES (:id, :poid, :type, :iid, :qty, :cost, :batch, :expiry)";
                
                $pdo->prepare($sql)->execute([
                    'id' => $itemId,
                    'poid' => $orderId,
                    'type' => $itemType,
                    'iid' => $item['item_id'],
                    'qty' => $item['quantity'],
                    'cost' => $item['unit_cost'],
                    'batch' => $item['batch_number'] ?? null,
                    'expiry' => $item['expiry_date'] ?? null
                ]);
            }

            $pdo->commit();
            return ['success' => true, 'order_id' => $orderId, 'order_number' => $orderNumber];

        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Procesa la recepción de mercancía de una orden de compra
     */
    public function receiveOrder(string $orderId, array $receivedItems): array
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $inventoryService = new InventoryService();
            $financeService = new FinanceService();

            // Obtener orden
            $stmt = $pdo->prepare("SELECT * FROM purchase_orders WHERE id = :id");
            $stmt->execute(['id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new \Exception("Orden de compra no encontrada");
            }

            $totalReceived = 0;
            $allItemsReceived = true;

            // Procesar cada item recibido
            foreach ($receivedItems as $itemId => $receivedQty) {
                if ($receivedQty <= 0) continue;

                // Obtener info del item
                $stmt = $pdo->prepare("SELECT * FROM purchase_order_items WHERE id = :id");
                $stmt->execute(['id' => $itemId]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$item) continue;

                // Actualizar cantidad recibida
                $newReceivedQty = $item['received_quantity'] + $receivedQty;
                $stmt = $pdo->prepare("UPDATE purchase_order_items SET received_quantity = :qty WHERE id = :id");
                $stmt->execute(['qty' => $newReceivedQty, 'id' => $itemId]);

                // Registrar entrada en inventario (FIFO)
                $inventoryService->registerEntry(
                    $item['item_type'],
                    $item['item_id'],
                    $receivedQty,
                    $item['unit_cost'],
                    $item['batch_number'],
                    $item['expiry_date']
                );

                // Actualizar stock del producto/materia prima
                $table = $item['item_type'] === 'product' ? 'products' : 'raw_materials';
                $stmt = $pdo->prepare("UPDATE {$table} SET stock = stock + :qty WHERE id = :id");
                $stmt->execute(['qty' => $receivedQty, 'id' => $item['item_id']]);

                $totalReceived += $receivedQty * $item['unit_cost'];

                // Verificar si el item está completamente recibido
                if ($newReceivedQty < $item['quantity']) {
                    $allItemsReceived = false;
                }
            }

            // Actualizar estado de la orden
            $newStatus = $allItemsReceived ? 'received' : 'partial';
            $stmt = $pdo->prepare("UPDATE purchase_orders SET status = :status, received_at = NOW() WHERE id = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $orderId]);

            // Crear CXP si la orden está completamente recibida
            if ($allItemsReceived && $totalReceived > 0) {
                // Calcular fecha de vencimiento basada en los términos de pago
                $days = (int) $order['payment_term_days'];
                $dueDate = ($days > 0) ? date('Y-m-d', strtotime("+$days days")) : date('Y-m-d');

                $financeService->registerCxp([
                    'supplier_id' => $order['supplier_id'],
                    'amount' => $totalReceived,
                    'due_date' => $dueDate,
                    'notes' => "Compra - Orden #{$order['order_number']}"
                ]);
            }

            $pdo->commit();
            return ['success' => true, 'status' => $newStatus];

        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Genera un número de orden único
     */
    protected function generateOrderNumber(string $businessId): string
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM purchase_orders WHERE business_id = :bid");
        $stmt->execute(['bid' => $businessId]);
        $count = $stmt->fetchColumn();
        
        return 'PO-' . date('Ymd') . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtiene órdenes pendientes de recepción
     */
    public function getPendingOrders(string $businessId): array
    {
        $pdo = Database::connect();
        $sql = "SELECT po.*, s.name as supplier_name 
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.business_id = :bid 
                AND po.status IN ('pending', 'partial')
                ORDER BY po.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene items de una orden de compra
     */
    public function getOrderItems(string $orderId): array
    {
        $pdo = Database::connect();
        $sql = "SELECT poi.*, 
                CASE 
                    WHEN poi.item_type = 'product' THEN p.name
                    WHEN poi.item_type = 'raw_material' THEN rm.name
                END as item_name
                FROM purchase_order_items poi
                LEFT JOIN products p ON poi.item_id = p.id AND poi.item_type = 'product'
                LEFT JOIN raw_materials rm ON poi.item_id = rm.id AND poi.item_type = 'raw_material'
                WHERE poi.purchase_order_id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
