<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use PDO;

class FinanceService
{
    public function registerCxp(array $data): bool
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO accounts_payable (id, business_id, supplier_id, amount, due_date, notes) 
                VALUES (UUID(), :bid, :sid, :amt, :due, :notes)";
        $stmt = $pdo->prepare($sql);
        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        return $stmt->execute([
            'bid' => $businessId,
            'sid' => $data['supplier_id'],
            'amt' => $data['amount'],
            'due' => $data['due_date'],
            'notes' => $data['notes'] ?? null
        ]);
    }

    public function registerCxc(array $data): bool
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO accounts_receivable (id, business_id, client_id, user_id, amount, due_date, notes) 
                VALUES (UUID(), :bid, :cid, :uid, :amt, :due, :notes)";
        $stmt = $pdo->prepare($sql);
        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        return $stmt->execute([
            'bid' => $businessId,
            'cid' => $data['client_id'] ?? null,
            'uid' => $data['user_id'] ?? null,
            'amt' => $data['amount'],
            'due' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Obtiene las cuentas por cobrar pendientes (Clientes y Empleados).
     */
    public function getPendingCxc(string $businessId): array
    {
        $pdo = Database::connect();
        $sql = "SELECT cxc.*, 
                       cl.name as client_name, 
                       u.name as employee_name,
                       o.total_price as order_total
                FROM accounts_receivable cxc
                LEFT JOIN clients cl ON cxc.client_id = cl.id
                LEFT JOIN users u ON cxc.user_id = u.id
                LEFT JOIN orders o ON cxc.order_id = o.id
                WHERE cxc.business_id = :bid AND cxc.status IN ('pending', 'partial')
                ORDER BY cxc.created_at ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una CXC por ID.
     */
    public function getCxcById(string $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM accounts_receivable WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Obtiene una CXP por ID.
     */
    public function getCxpById(string $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM accounts_payable WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Registra un pago/abono a una CXC.
     */
    public function recordCxcPayment(string $cxcId, array $data): bool
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            // 1. Obtener CXC
            $stmt = $pdo->prepare("SELECT * FROM accounts_receivable WHERE id = :id");
            $stmt->execute(['id' => $cxcId]);
            $cxc = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$cxc)
                throw new \Exception("Cuenta no encontrada.");

            $newPaidAmount = $cxc['paid_amount'] + $data['amount'];
            $status = ($newPaidAmount >= $cxc['amount']) ? 'paid' : 'partial';

            $businessId = \OmniPOS\Services\TenantService::getBusinessId();

            // 2. Actualizar CXC con Scope de Negocio
            $stmt = $pdo->prepare("UPDATE accounts_receivable SET paid_amount = :paid, status = :status WHERE id = :id AND business_id = :bid");
            $stmt->execute([
                'paid' => $newPaidAmount,
                'status' => $status,
                'id' => $cxcId,
                'bid' => $businessId
            ]);

            // 3. Registrar Transacción en Caja (si aplica sesión)
            if (!empty($data['cash_session_id'])) {
                $transaction = new \OmniPOS\Models\Transaction();
                $transaction->create([
                    'business_id' => $businessId,
                    'cash_session_id' => $data['cash_session_id'],
                    'type' => 'income',
                    'amount' => $data['amount'],
                    'currency' => $data['currency'] ?? 'USD',
                    'payment_method_id' => $data['payment_method_id'],
                    'reference_type' => 'debt_payment',
                    'reference_id' => $cxcId,
                    'description' => "Abono a CXC #" . substr($cxcId, 0, 8),
                    'created_by' => Session::get('user_id')
                ]);
            }

            // 4. Registrar Asiento Contable
            $accountingService = new AccountingService();
            $accountingService->recordEntry([
                'business_id' => $businessId,
                'description' => "Cobro de CXC #" . substr($cxcId, 0, 8),
                'reference_type' => 'sale', // O 'adjustment'
                'reference_id' => $cxcId,
                'accounts' => [
                    ['name' => 'Cuentas por Cobrar', 'credit' => $data['amount']],
                    ['name' => 'Caja/Banco', 'debit' => $data['amount']]
                ]
            ]);

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Obtiene las cuentas por pagar pendientes (Proveedores).
     */
    public function getPendingCxp(string $businessId): array
    {
        $pdo = Database::connect();
        $sql = "SELECT cxp.*, 
                       s.name as supplier_name,
                       po.total_cost as po_total
                FROM accounts_payable cxp
                LEFT JOIN suppliers s ON cxp.supplier_id = s.id
                LEFT JOIN purchase_orders po ON cxp.purchase_order_id = po.id
                WHERE cxp.business_id = :bid AND cxp.status IN ('pending', 'partial')
                ORDER BY cxp.created_at ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Registra un pago/abono a una CXP.
     */
    public function recordCxpPayment(string $cxpId, array $data): bool
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            // 1. Obtener CXP
            $stmt = $pdo->prepare("SELECT * FROM accounts_payable WHERE id = :id");
            $stmt->execute(['id' => $cxpId]);
            $cxp = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$cxp)
                throw new \Exception("Cuenta por pagar no encontrada.");

            $newPaidAmount = $cxp['paid_amount'] + $data['amount'];
            $status = ($newPaidAmount >= $cxp['amount']) ? 'paid' : 'partial';

            $businessId = \OmniPOS\Services\TenantService::getBusinessId();

            // 2. Actualizar CXP con Scope de Negocio
            $stmt = $pdo->prepare("UPDATE accounts_payable SET paid_amount = :paid, status = :status WHERE id = :id AND business_id = :bid");
            $stmt->execute([
                'paid' => $newPaidAmount,
                'status' => $status,
                'id' => $cxpId,
                'bid' => $businessId
            ]);

            // 3. Registrar Transacción de Egreso en Caja (si aplica sesión)
            if (!empty($data['cash_session_id'])) {
                $transaction = new \OmniPOS\Models\Transaction();
                $transaction->create([
                    'business_id' => $businessId,
                    'cash_session_id' => $data['cash_session_id'],
                    'type' => 'expense',
                    'amount' => $data['amount'],
                    'currency' => $data['currency'] ?? 'USD',
                    'payment_method_id' => $data['payment_method_id'],
                    'reference_type' => 'purchase',
                    'reference_id' => $cxpId,
                    'description' => "Pago a CXP #" . substr($cxpId, 0, 8),
                    'created_by' => Session::get('user_id')
                ]);
            }

            // 4. Registrar Asiento Contable
            $accountingService = new AccountingService();
            $accountingService->recordEntry([
                'business_id' => $businessId,
                'description' => "Pago a CXP #" . substr($cxpId, 0, 8),
                'reference_type' => 'purchase',
                'reference_id' => $cxpId,
                'accounts' => [
                    ['name' => 'Cuentas por Pagar', 'debit' => $data['amount']],
                    ['name' => 'Caja/Banco', 'credit' => $data['amount']]
                ]
            ]);

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
