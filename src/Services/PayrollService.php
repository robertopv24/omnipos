<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use PDO;

class PayrollService
{
    /**
     * Registra un pago de nómina.
     */
    public function recordPayment(array $data): bool
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $id = $pdo->query("SELECT UUID()")->fetchColumn();

            $sql = "INSERT INTO payroll_payments (id, business_id, user_id, amount, deductions_amount, payment_date, payment_method_id, notes, created_by) 
                    VALUES (:id, :bid, :uid, :amt, :ded, :date, :pmid, :notes, :created_by)";

            $stmt = $pdo->prepare($sql);
            $businessId = \OmniPOS\Services\TenantService::getBusinessId();
            $stmt->execute([
                'id' => $id,
                'bid' => $businessId,
                'uid' => $data['user_id'],
                'amt' => $data['amount'],
                'ded' => $data['deductions_amount'] ?? 0,
                'date' => $data['payment_date'] ?? date('Y-m-d'),
                'pmid' => $data['payment_method_id'],
                'notes' => $data['notes'] ?? null,
                'created_by' => Session::get('user_id')
            ]);

            // 1. Registrar Transacción de Egreso en Caja (si aplica sesión)
            if (!empty($data['cash_session_id'])) {
                $transaction = new \OmniPOS\Models\Transaction();
                $transaction->create([
                    'business_id' => $businessId,
                    'cash_session_id' => $data['cash_session_id'],
                    'type' => 'expense',
                    'amount' => $data['amount'],
                    'currency' => $data['currency'] ?? 'USD',
                    'payment_method_id' => $data['payment_method_id'],
                    'reference_type' => 'manual', // O 'payroll' si añadimos el enum
                    'reference_id' => $id,
                    'description' => "Pago de Nómina - Emp ID: " . substr($data['user_id'], 0, 8),
                    'created_by' => Session::get('user_id')
                ]);
            }

            // 2. Registrar Asiento Contable
            $accountingService = new AccountingService();
            $accountingService->recordEntry([
                'business_id' => $businessId,
                'description' => "Pago de Nómina - Emp ID: " . substr($data['user_id'], 0, 8),
                'reference_type' => 'payroll',
                'reference_id' => $id,
                'accounts' => [
                    ['name' => 'Gastos de Personal', 'debit' => $data['amount'] + ($data['deductions_amount'] ?? 0)],
                    ['name' => 'Caja/Banco', 'credit' => $data['amount']],
                    ['name' => 'Cuentas por Cobrar (Beneficios)', 'credit' => $data['deductions_amount'] ?? 0]
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
     * Obtiene el historial de pagos de nómina.
     */
    public function getPayments(string $businessId): array
    {
        $pdo = Database::connect();
        $sql = "SELECT p.*, u.name as employee_name, cb.name as creator_name
                FROM payroll_payments p
                JOIN users u ON p.user_id = u.id
                JOIN users cb ON p.created_by = cb.id
                WHERE p.business_id = :bid
                ORDER BY p.payment_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
