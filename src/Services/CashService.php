<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use PDO;

class CashService
{
    /**
     * Obtiene la sesión de caja abierta para el usuario actual.
     */
    public function getOpenSession(string $userId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM cash_sessions WHERE user_id = :uid AND status = 'open' LIMIT 1");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Abre una nueva sesión de caja.
     */
    public function openSession(array $data): bool
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO cash_sessions (id, business_id, user_id, opening_balance_usd, opening_balance_ves, status, opened_at) 
                VALUES (UUID(), :bid, :uid, :usd, :ves, 'open', CURRENT_TIMESTAMP())";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'bid' => Session::get('business_id'),
            'uid' => Session::get('user_id'),
            'usd' => $data['opening_balance_usd'] ?? 0,
            'ves' => $data['opening_balance_ves'] ?? 0
        ]);
    }

    /**
     * Calcula el saldo esperado en caja basado en transacciones.
     */
    public function getCalculatedBalances(string $sessionId): array
    {
        $pdo = Database::connect();

        // Sumar ingresos y restar egresos por moneda
        $sql = "SELECT 
                    currency,
                    SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as total
                FROM transactions 
                WHERE cash_session_id = :sid
                GROUP BY currency";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['sid' => $sessionId]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $session = $this->getSessionById($sessionId);

        return [
            'usd' => ($session['opening_balance_usd'] ?? 0) + ($results['USD'] ?? 0),
            'ves' => ($session['opening_balance_ves'] ?? 0) + ($results['VES'] ?? 0)
        ];
    }

    /**
     * Cierra la sesión de caja con el arqueo manual.
     */
    public function closeSession(string $sessionId, array $closingData): bool
    {
        $pdo = Database::connect();
        $balances = $this->getCalculatedBalances($sessionId);

        $sql = "UPDATE cash_sessions SET 
                    closing_balance_usd = :close_usd,
                    closing_balance_ves = :close_ves,
                    calculated_usd = :calc_usd,
                    calculated_ves = :calc_ves,
                    status = 'closed',
                    closed_at = CURRENT_TIMESTAMP()
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'close_usd' => $closingData['closing_balance_usd'],
            'close_ves' => $closingData['closing_balance_ves'],
            'calc_usd' => $balances['usd'],
            'calc_ves' => $balances['ves'],
            'id' => $sessionId
        ]);
    }

    /**
     * Registra un movimiento manual de caja chica (ingreso/egreso).
     */
    public function registerMovement(array $data): bool
    {
        $pdo = Database::connect();
        $session = $this->getOpenSession(Session::get('user_id'));

        if (!$session)
            return false;

        $accountingService = new AccountingService();
        $rate = $accountingService->getExchangeRate(Session::get('business_id'));

        $sql = "INSERT INTO transactions (id, business_id, cash_session_id, type, amount, currency, exchange_rate, amount_usd_ref, payment_method_id, reference_type, description, created_by) 
                VALUES (UUID(), :bid, :sid, :type, :amt, :curr, :rate, :ref, :pmid, 'manual', :desc, :uid)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'bid' => Session::get('business_id'),
            'sid' => $session['id'],
            'type' => $data['type'], // income or expense
            'amt' => $data['amount'],
            'curr' => $data['currency'],
            'rate' => $rate,
            'ref' => ($data['currency'] === 'USD') ? $data['amount'] : ($data['amount'] / $rate),
            'pmid' => $data['payment_method_id'],
            'desc' => $data['description'],
            'uid' => Session::get('user_id')
        ]);
    }

    private function getSessionById(string $id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM cash_sessions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
