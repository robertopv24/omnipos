<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;

class AccountingService
{
    /**
     * Obtiene la tasa de cambio efectiva para una fecha dada a través del servicio dedicado.
     */
    public function getExchangeRate(string $businessId, ?string $date = null): float
    {
        $rateService = new ExchangeRateService();
        return $rateService->getExchangeRate($businessId, $date);
    }

    /**
     * Registra un asiento en el libro diario.
     */
    public function recordEntry(array $data): void
    {
        $pdo = Database::connect();
        $rate = $this->getExchangeRate($data['business_id']);

        $sql = "INSERT INTO accounting_ledger (id, business_id, entry_date, description, reference_type, reference_id, account_name, debit, credit, exchange_rate) 
                VALUES (UUID(), :bid, :date, :desc, :ref_type, :ref_id, :acc, :debit, :credit, :rate)";

        $stmt = $pdo->prepare($sql);
        foreach ($data['accounts'] as $entry) {
            $stmt->execute([
                'bid' => $data['business_id'],
                'date' => $data['date'] ?? date('Y-m-d'),
                'desc' => $data['description'],
                'ref_type' => $data['reference_type'],
                'ref_id' => $data['reference_id'],
                'acc' => $entry['name'],
                'debit' => $entry['debit'] ?? 0,
                'credit' => $entry['credit'] ?? 0,
                'rate' => $rate
            ]);
        }
    }

    /**
     * Registra una transferencia de capital entre cuentas/cajas.
     */
    public function transferCapital(array $data): bool
    {
        $pdo = Database::connect();
        $rate = $this->getExchangeRate(Session::get('business_id'));

        $pdo->beginTransaction();
        try {
            $sql = "INSERT INTO capital_transfers (id, business_id, from_account_id, to_account_id, amount, currency, exchange_rate, created_by, notes) 
                    VALUES (UUID(), :bid, :from, :to, :amt, :curr, :rate, :uid, :notes)";

            $pdo->prepare($sql)->execute([
                'bid' => Session::get('business_id'),
                'from' => $data['from_account_id'],
                'to' => $data['to_account_id'],
                'amt' => $data['amount'],
                'curr' => $data['currency'],
                'rate' => $rate,
                'uid' => Session::get('user_id'),
                'notes' => $data['notes'] ?? null
            ]);

            // Registrar en el libro contable
            $this->recordEntry([
                'business_id' => Session::get('business_id'),
                'description' => "Transferencia de Capital: " . ($data['notes'] ?? 'Sin descripción'),
                'reference_type' => 'transfer',
                'reference_id' => $pdo->query("SELECT @last_uuid")->fetchColumn() ?: 'INTERNAL_REF',
                'accounts' => [
                    ['name' => $data['from_account_name'], 'credit' => $data['amount']],
                    ['name' => $data['to_account_name'], 'debit' => $data['amount']]
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
     * Obtiene métricas financieras consolidadas para una cuenta (múltiples negocios).
     */
    public function getConsolidatedMetrics(string $accountId): array
    {
        $pdo = Database::connect();

        // 1. Total Ventas Hoy
        $sqlSales = "SELECT SUM(o.total_price) as total 
                     FROM orders o 
                     JOIN businesses b ON o.business_id = b.id 
                     WHERE b.account_id = :aid AND DATE(o.created_at) = CURDATE()";
        $stmt = $pdo->prepare($sqlSales);
        $stmt->execute(['aid' => $accountId]);
        $totalSales = $stmt->fetchColumn();

        // 2. Total CXC Pendiente
        $sqlCxc = "SELECT SUM(amount - paid_amount) as total 
                   FROM accounts_receivable ar
                   JOIN businesses b ON ar.business_id = b.id 
                   WHERE b.account_id = :aid AND ar.status IN ('pending', 'partial')";
        $stmt = $pdo->prepare($sqlCxc);
        $stmt->execute(['aid' => $accountId]);
        $totalCxc = $stmt->fetchColumn();

        // 3. Saldo en Caja (Opening balance + Transactions)
        $sqlCash = "SELECT 
                    (SELECT IFNULL(SUM(opening_balance_usd), 0) FROM cash_sessions cs JOIN businesses b ON cs.business_id = b.id WHERE b.account_id = :aid1 AND cs.status = 'open') +
                    (SELECT IFNULL(SUM(CASE WHEN t.type = 'income' THEN t.amount_usd_ref ELSE -t.amount_usd_ref END), 0) 
                     FROM transactions t 
                     JOIN cash_sessions cs ON t.cash_session_id = cs.id 
                     JOIN businesses b ON cs.business_id = b.id
                     WHERE b.account_id = :aid2 AND cs.status = 'open')";
        $stmt = $pdo->prepare($sqlCash);
        $stmt->execute(['aid1' => $accountId, 'aid2' => $accountId]);
        $totalCash = $stmt->fetchColumn();

        // 4. Métricas por Negocio (Comparativa)
        $sqlByBusiness = "SELECT b.id, b.name, 
                                 IFNULL(SUM(o.total_price), 0) as daily_sales,
                                 (SELECT IFNULL(SUM(amount - paid_amount), 0) FROM accounts_receivable WHERE business_id = b.id AND status IN ('pending', 'partial')) as pending_cxc
                          FROM businesses b
                          LEFT JOIN orders o ON b.id = o.business_id AND DATE(o.created_at) = CURDATE()
                          WHERE b.account_id = :aid
                          GROUP BY b.id";
        $stmt = $pdo->prepare($sqlByBusiness);
        $stmt->execute(['aid' => $accountId]);
        $businessStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'total_sales' => (float) $totalSales,
            'total_cxc' => (float) $totalCxc,
            'total_cash' => (float) $totalCash,
            'business_stats' => $businessStats
        ];
    }
}
