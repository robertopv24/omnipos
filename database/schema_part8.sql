-- MariaDB 10.11+ Schema - Part 8: Accounting & Historical Currency Exchange

-- --------------------------------------------------------
-- Exchange Rate History (Histórico de Tasas por Negocio)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `exchange_rates` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `currency_from` CHAR(3) NOT NULL DEFAULT 'USD',
  `currency_to` CHAR(3) NOT NULL DEFAULT 'VES',
  `rate` DECIMAL(20,6) NOT NULL,
  `effective_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_rate_per_day` (`business_id`, `currency_from`, `currency_to`, `effective_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- General Ledger (Libro Diario / Contable)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `accounting_ledger` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `entry_date` DATE NOT NULL,
  `description` TEXT NOT NULL,
  `reference_type` ENUM('sale', 'purchase', 'transfer', 'payroll', 'adjustment', 'production') NOT NULL,
  `reference_id` UUID NOT NULL,
  `account_name` VARCHAR(100) NOT NULL COMMENT 'Ej: Caja Chica, Banco Provincial, Inventario',
  `debit` DECIMAL(20,6) DEFAULT 0.000000,
  `credit` DECIMAL(20,6) DEFAULT 0.000000,
  `exchange_rate` DECIMAL(20,6) NOT NULL DEFAULT 1.000000,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Capital Transfers (Transferencias entre Cuentas)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `capital_transfers` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `from_account_id` UUID NOT NULL COMMENT 'Origen (ej: Caja sesión 01)',
  `to_account_id` UUID NOT NULL COMMENT 'Destino (ej: Caja General o Banco)',
  `amount` DECIMAL(20,6) NOT NULL,
  `currency` CHAR(3) NOT NULL,
  `exchange_rate` DECIMAL(20,6) NOT NULL,
  `transfer_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'completed', 'rejected') DEFAULT 'completed',
  `created_by` UUID NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Actualización de Órdenes para Tasa de Cambio
-- --------------------------------------------------------
ALTER TABLE `orders` 
ADD COLUMN IF NOT EXISTS `exchange_rate` DECIMAL(20,6) NOT NULL DEFAULT 1.000000;

ALTER TABLE `transactions`
ADD COLUMN IF NOT EXISTS `exchange_rate` DECIMAL(20,6) NOT NULL DEFAULT 1.000000;
