<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;

class ExchangeRateService
{
    /**
     * Obtiene la tasa de cambio efectiva para una fecha dada.
     */
    public function getExchangeRate(?string $businessId = null, ?string $date = null): float
    {
        $businessId = $businessId ?: Session::get('business_id');
        $date = $date ?: date('Y-m-d');
        
        if (!$businessId) return 1.0;

        $pdo = Database::connect();

        $sql = "SELECT rate FROM exchange_rates 
                WHERE business_id = :bid AND effective_date <= :date 
                ORDER BY effective_date DESC LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId, 'date' => $date]);
        $rate = $stmt->fetchColumn();

        // Fallback a la configuración global/negocio si no hay entrada en exchange_rates
        if (!$rate) {
            $rate = ConfigService::getInstance()->get('exchange_rate', 1.0);
        }

        return (float) $rate;
    }

    /**
     * Convierte de moneda local (VES) a base (USD).
     */
    public function convertToBase(float $amount, string $currency, ?string $businessId = null): float
    {
        if ($currency === 'USD') return $amount;
        
        $rate = $this->getExchangeRate($businessId);
        return $rate > 0 ? $amount / $rate : $amount;
    }

    /**
     * Convierte de base (USD) a moneda local (VES).
     */
    public function convertFromBase(float $amount, string $currency, ?string $businessId = null): float
    {
        if ($currency === 'USD') return $amount;

        $rate = $this->getExchangeRate($businessId);
        return $amount * $rate;
    }

    /**
     * Formatea un monto según la configuración regional.
     */
    public function formatCurrency(float $amount, string $currency = 'USD'): string
    {
        $symbol = ($currency === 'USD') ? '$' : 'Bs.';
        return $symbol . ' ' . number_format($amount, 2, ',', '.');
    }
}
