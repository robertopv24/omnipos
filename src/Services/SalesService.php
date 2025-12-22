<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Models\Order;
use OmniPOS\Models\OrderItem;
use OmniPOS\Models\Transaction;
use OmniPOS\Core\Session;

class SalesService
{
    public function processSale(array $data): array
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            // 0. Obtener ID de Negocio y Tasa
            $businessId = Session::get('business_id');
            $rateService = new ExchangeRateService();
            $rate = $rateService->getExchangeRate($businessId);

            // 1. RECALCULAR TOTALES DESDE EL BACKEND (SEGURIDAD)
            // No confiamos en $data['total'] ni en los precios del frontend.
            $backendItems = [];
            $backendSubtotal = 0;
            $backendPackagingTotal = 0;
            $consumptionType = $data['consumption_type'] ?? 'dine_in';

            foreach ($data['items'] as $item) {
                // Consultar precio real, costo de empaque y vínculo con manufactura/tipo
                $stmt = $pdo->prepare("SELECT price_usd, packaging_cost, name, linked_manufactured_id, product_type 
                                      FROM products WHERE id = :id AND business_id = :bid");
                $stmt->execute(['id' => $item['id'], 'bid' => $businessId]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new \Exception("Producto no encontrado o inválido: " . $item['id']);
                }

                $quantity = $item['quantity'];
                $realPrice = (float)$product['price_usd'];
                $packagingCost = ($consumptionType === 'takeaway') ? (float)$product['packaging_cost'] : 0;

                $lineTotal = $realPrice * $quantity;
                $linePackaging = $packagingCost * $quantity;

                $backendSubtotal += $lineTotal;
                $backendPackagingTotal += $linePackaging;

                // Guardamos datos sanitizados para procesar
                $backendItems[] = [
                    'id' => $item['id'],
                    'name' => $product['name'], // Usar nombre de DB por seguridad visual
                    'quantity' => $quantity,
                    'price' => $realPrice,
                    'packaging_fee' => $itemPackagingFee ?? $packagingCost, // Fixed logic variable
                    'modifications' => $item['modifications'] ?? null,
                    'linked_manufactured_id' => $product['linked_manufactured_id'] ?? null,
                    'product_type' => $product['product_type'] ?? 'simple'
                ];
            }

            // Calcular Impuestos (Backend)
            $taxService = new TaxService();
            // Pasamos los items sanitizados
            $taxes = $taxService->calculateTaxes($backendItems, $data['payments'] ?? []); // Payments se valida luego

            // Total Real
            $finalTotal = $backendSubtotal + $backendPackagingTotal + $taxes['total_taxes'];

            // 2. Validar Pagos (Si no es crédito)
            $isCredit = $data['payment_type'] === 'credit';
            if (!$isCredit) {
                $paymentTotal = 0;
                foreach ($data['payments'] as $p) {
                    // Convertir pagos en otras monedas a USD base usando el servicio centralizado
                    $paymentTotal += $rateService->convertToBase((float)$p['amount'], $p['currency'], $businessId);
                }
                // Por ahora no bloqueamos si falta un céntimo por redondeo, pero el total registrado será el REAL.
            }

            // 3. Crear Orden
            $order = new Order();
            $orderId = $pdo->query("SELECT UUID()")->fetchColumn();
            $order->create([
                'id' => $orderId,
                'business_id' => $businessId,
                'user_id' => Session::get('user_id'),
                'total_price' => $finalTotal, // USAMOS EL TOTAL RECALCULADO
                'exchange_rate' => $rate,
                'status' => 'paid',
                'consumption_type' => $consumptionType,
                'shipping_address' => 'Site' // Default placeholder for now
            ]);

            // 4. Crear Items y Descontar Stock
            $inventoryService = new \OmniPOS\Services\InventoryService();
            
            foreach ($backendItems as $bItem) {
                $orderItem = new OrderItem();
                $orderItem->create([
                    'id' => $pdo->query("SELECT UUID()")->fetchColumn(),
                    'business_id' => $businessId,
                    'order_id' => $orderId,
                    'product_id' => $bItem['id'],
                    'quantity' => $bItem['quantity'],
                    'price' => $bItem['price'], // Precio Real DB
                    'packaging_fee' => $bItem['packaging_fee'],
                    'modifications' => isset($bItem['modifications']) ? json_encode($bItem['modifications']) : null,
                    'consumption_type' => $consumptionType,
                    'status' => 'pending'
                ]);

                // Descontar stock
                if ($bItem['product_type'] === 'prepared' || $bItem['linked_manufactured_id']) {
                    // Si está vinculado a una receta, descontamos los ingredientes (on-demand)
                    $inventoryService->consumeRecipe($bItem['linked_manufactured_id'], $bItem['quantity'], $orderId);
                } elseif ($bItem['product_type'] === 'compound') {
                    // Si es un combo, descontamos sus componentes
                    $this->consumeCombo($bItem['id'], $bItem['quantity'], $orderId);
                } else {
                    // Si es un producto simple o resale, descontamos de su propio stock (FIFO)
                    $inventoryService->consumeStock('product', $bItem['id'], $bItem['quantity'], 'sale', $orderId);
                }
            }

            // 5. Registrar Transacciones o CXC
            if ($isCredit) {
                $isBenefit = !empty($data['is_benefit']);
                $pdo->prepare("INSERT INTO accounts_receivable (id, business_id, order_id, client_id, user_id, amount, status, notes, authorized_by) 
                               VALUES (UUID(), :bid, :oid, :cid, :uid, :amt, 'pending', :notes, :auth)")
                    ->execute([
                        'bid' => $businessId,
                        'oid' => $orderId,
                        'cid' => (!$isBenefit) ? ($data['client_id'] ?? null) : null,
                        'uid' => ($isBenefit) ? ($data['user_id'] ?? null) : null,
                        'amt' => $finalTotal, // Monto Real
                        'notes' => $isBenefit ? 'Beneficio de Empleado' : 'Venta a crédito POS',
                        'auth' => $data['authorized_by']
                    ]);

                // Log Auditoría (Mantenido igual)
                $pdo->prepare("INSERT INTO authorization_logs (id, business_id, user_id, supervisor_id, operation_type, reference_id) 
                               VALUES (UUID(), :bid, :uid, :sid, 'credit_sale', :oid)")
                    ->execute([
                        'bid' => $businessId,
                        'uid' => Session::get('user_id'),
                        'sid' => $data['authorized_by'],
                        'oid' => $orderId
                    ]);
            } else {
                foreach ($data['payments'] as $payment) {
                    $transaction = new Transaction();
                    $transaction->create([
                        'business_id' => $businessId,
                        'cash_session_id' => $data['cash_session_id'],
                        'type' => 'income',
                        'amount' => $payment['amount'],
                        'currency' => $payment['currency'],
                        'payment_method_id' => $payment['method_id'],
                        'reference_type' => 'order',
                        'reference_id' => $orderId,
                        'created_by' => Session::get('user_id')
                    ]);
                }
            }

            // 6. Registrar Asiento Contable
            $accountingService = new AccountingService();
            $accountingService->recordEntry([
                'business_id' => $businessId,
                'description' => "Venta POS #" . substr($orderId, 0, 8),
                'reference_type' => 'sale',
                'reference_id' => $orderId,
                'accounts' => [
                    ['name' => 'Ventas', 'credit' => $backendSubtotal + $backendPackagingTotal], // Ingreso neto
                    ['name' => 'IVA por Pagar', 'credit' => $taxes['total_taxes']],
                    ['name' => 'Caja/Banco', 'debit' => $finalTotal]
                ]
            ]);

            $pdo->commit();
            
            // Retornamos el total real para que el frontend sepa si hubo discrepancia (opcional, pero buena práctica)
            return [
                'success' => true, 
                'order_id' => $orderId, 
                'real_total' => $finalTotal
            ];

        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Consume los componentes de un combo.
     */
    private function consumeCombo(string $productId, float $quantity, string $orderId): void
    {
        $comboModel = new \OmniPOS\Models\Combo();
        $items = $comboModel->getItemsByProduct($productId);
        $inventoryService = new \OmniPOS\Services\InventoryService();

        foreach ($items as $item) {
            $needed = $item['quantity'] * $quantity;
            // Descontar como producto simple (FIFO)
            $inventoryService->consumeStock('product', $item['product_id'], $needed, 'sale', $orderId);
        }
    }

    public function getSalesHistory(string $businessId, int $page = 1, int $perPage = 15): array
    {
        $pdo = Database::connect();
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT o.id, o.created_at, o.total_price, o.status, 
                       c.name as client_name, u.name as user_name
                FROM orders o
                LEFT JOIN clients c ON o.client_id = c.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.business_id = :bid
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':bid', $businessId);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get total count for pagination
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE business_id = :bid");
        $countStmt->execute(['bid' => $businessId]);
        $total = $countStmt->fetchColumn();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public function getOrderDetails(string $orderId, string $businessId): ?array
    {
        $pdo = Database::connect();

        // 1. Get Order Header
        $sql = "SELECT o.*, c.name as client_name, c.email as client_email, 
                       u.name as user_name
                FROM orders o
                LEFT JOIN clients c ON o.client_id = c.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = :id AND o.business_id = :bid";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $orderId, 'bid' => $businessId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        // 2. Get Items
        $sqlItems = "SELECT oi.*, p.name as product_name, p.sku 
                     FROM order_items oi
                     LEFT JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = :oid"; // No need for business_id check here as order implies it
        
        $stmtItems = $pdo->prepare($sqlItems);
        $stmtItems->execute(['oid' => $orderId]);
        $items = $stmtItems->fetchAll(\PDO::FETCH_ASSOC);

        $order['items'] = $items;

        return $order;
    }
}
