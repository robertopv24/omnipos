-- MariaDB 10.11+ Schema - Part 5: Tax System and Regimes

-- --------------------------------------------------------
-- Tax Rates (Tasas de Impuestos)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tax_rates` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(50) NOT NULL, -- Ej: IVA General, IVA Reducido, Exento
  `percentage` DECIMAL(5,2) NOT NULL,
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Inyección de Regimen en el Negocio
-- --------------------------------------------------------
ALTER TABLE `businesses` 
ADD COLUMN IF NOT EXISTS `tax_regime` ENUM('ordinario', 'especial', 'exento') DEFAULT 'ordinario',
ADD COLUMN IF NOT EXISTS `rif` VARCHAR(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `igtf_percentage` DECIMAL(5,2) DEFAULT 3.00;

-- --------------------------------------------------------
-- Tax Ledger (Libro de Ventas / Impuestos)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tax_ledger` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `reference_type` ENUM('sale', 'purchase') NOT NULL,
  `reference_id` UUID NOT NULL,
  `tax_rate_id` UUID NOT NULL,
  `tax_base` DECIMAL(20,6) NOT NULL,
  `tax_amount` DECIMAL(20,6) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Productos: Vínculo con Tasa de Impuesto
-- --------------------------------------------------------
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `tax_rate_id` UUID DEFAULT NULL,
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates`(`id`) ON DELETE SET NULL;
