<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;

class TaxService
{
    /**
     * Calcula los impuestos de una orden basado en los ítems y métodos de pago.
     */
    public function calculateTaxes(array $items, array $payments): array
    {
        $taxTotal = 0;
        $igtfTotal = 0;
        $pdo = Database::connect();

        // 1. IVA (Basado en productos)
        foreach ($items as $item) {
            $stmt = $pdo->prepare("SELECT tr.percentage FROM products p 
                                  LEFT JOIN tax_rates tr ON p.tax_rate_id = tr.id 
                                  WHERE p.id = :id");
            $stmt->execute(['id' => $item['id']]);
            $rate = $stmt->fetchColumn() ?: 0;

            if ($rate > 0) {
                $itemTax = ($item['price'] * $item['quantity']) * ($rate / 100);
                $taxTotal += $itemTax;
            }
        }

        // 2. IGTF (Basado en pagos en moneda extranjera, ej: USD)
        $igtfRate = \OmniPOS\Services\ConfigService::getInstance()->get('igtf_percentage', 3.0);
        foreach ($payments as $payment) {
            if ($payment['currency'] === 'USD') {
                $igtfTotal += $payment['amount'] * ($igtfRate / 100);
            }
        }

        return [
            'iva' => $taxTotal,
            'igtf' => $igtfTotal,
            'total_taxes' => $taxTotal + $igtfTotal
        ];
    }

    public function recordTaxEntry(string $referenceId, string $rateId, float $base, float $amount)
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO tax_ledger (id, business_id, reference_type, reference_id, tax_rate_id, tax_base, tax_amount) 
                VALUES (UUID(), :bid, 'sale', :rid, :rate, :base, :amt)";
        $pdo->prepare($sql)->execute([
            'bid' => Session::get('business_id'),
            'rid' => $referenceId,
            'rate' => $rateId,
            'base' => $base,
            'amt' => $amount
        ]);
    }
}
