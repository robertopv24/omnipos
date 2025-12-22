-- MariaDB 10.11+ Schema - Part 3: Manufacture System

-- --------------------------------------------------------
-- Manufactured Products
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `manufactured_products` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'und',
  `stock` DECIMAL(20,6) DEFAULT 0.000000,
  `unit_cost_average` DECIMAL(20,6) DEFAULT 0.000000,
  `last_production_date` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Production Recipes
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `production_recipes` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `manufactured_product_id` UUID NOT NULL,
  `raw_material_id` UUID NOT NULL,
  `quantity_required` DECIMAL(20,6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_manufactured_product` (`manufactured_product_id`),
  FOREIGN KEY (`manufactured_product_id`) REFERENCES `manufactured_products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Production Orders
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `production_orders` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `manufactured_product_id` UUID NOT NULL,
  `quantity_produced` DECIMAL(20,6) NOT NULL,
  `labor_cost` DECIMAL(20,6) DEFAULT 0.000000,
  `total_cost` DECIMAL(20,6) NOT NULL,
  `created_by` UUID NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_manufactured_product` (`manufactured_product_id`),
  FOREIGN KEY (`manufactured_product_id`) REFERENCES `manufactured_products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
