<?php

namespace OmniPOS\Services;

class LocalizationService
{
    protected static array $formats = [
        'VE' => ['currency' => 'Bs', 'separator' => ',', 'decimal' => '.', 'date' => 'd/m/Y'],
        'CO' => ['currency' => 'COP', 'separator' => '.', 'decimal' => ',', 'date' => 'd/m/Y'],
        'US' => ['currency' => '$', 'separator' => ',', 'decimal' => '.', 'date' => 'm/d/Y'],
        'ES' => ['currency' => '€', 'separator' => '.', 'decimal' => ',', 'date' => 'd/m/Y'],
        'DEFAULT' => ['currency' => '$', 'separator' => ',', 'decimal' => '.', 'date' => 'Y-m-d']
    ];

    protected static string $country = 'DEFAULT';

    public static function setCountry(string $country): void
    {
        self::$country = strtoupper($country);
    }

    public static function formatCurrency(float $amount): string
    {
        $f = self::$formats[self::$country] ?? self::$formats['DEFAULT'];
        return $f['currency'] . " " . number_format($amount, 2, $f['decimal'], $f['separator']);
    }

    public static function formatDate(string $date): string
    {
        $f = self::$formats[self::$country] ?? self::$formats['DEFAULT'];
        return date($f['date'], strtotime($date));
    }
}
