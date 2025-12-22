-- MariaDB 10.11+ Schema - Part 7: Traceability (FIFO) & Batches

-- --------------------------------------------------------
-- Inventory Batches (Lotes de Inventario)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventory_batches` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `item_type` ENUM('product', 'raw_material') NOT NULL,
  `item_id` UUID NOT NULL,
  `batch_number` VARCHAR(50) DEFAULT NULL,
  `initial_quantity` DECIMAL(20,6) NOT NULL,
  `current_quantity` DECIMAL(20,6) NOT NULL,
  `unit_cost` DECIMAL(20,6) NOT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `received_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  `supplier_id` UUID DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Inventory Movements (Movimientos Detallados de Lote)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `batch_id` UUID NOT NULL,
  `type` ENUM('entry', 'exit', 'adjustment', 'discard') NOT NULL,
  `quantity` DECIMAL(20,6) NOT NULL,
  `reference_type` ENUM('sale', 'production', 'purchase', 'manual', 'internal_usage') NOT NULL,
  `reference_id` UUID DEFAULT NULL,
  `created_by` UUID NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`batch_id`) REFERENCES `inventory_batches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Actualización de Productos e Insumos
-- --------------------------------------------------------
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `category_type` ENUM('resale', 'operational_supply') DEFAULT 'resale';

ALTER TABLE `raw_materials`
ADD COLUMN IF NOT EXISTS `is_operational_supply` TINYINT(1) DEFAULT 0;
