
-- Additional Tables required for full migration

-- --------------------------------------------------------
-- Order Items
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `order_id` UUID NOT NULL,
  `product_id` UUID NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `consumption_type` ENUM('dine_in','takeaway','delivery') DEFAULT 'dine_in',
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_product_id` (`product_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Suppliers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `contact_person` VARCHAR(255),
  `email` VARCHAR(255),
  `phone` VARCHAR(20),
  `address` TEXT,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Raw Materials (Ingredients)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `raw_materials` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `unit` VARCHAR(20) NOT NULL,
  `stock_quantity` DECIMAL(20,6) DEFAULT 0,
  `cost_per_unit` DECIMAL(20,6) DEFAULT 0,
  `min_stock` DECIMAL(20,6) DEFAULT 5,
  `category` ENUM('ingredient','packaging','supply') DEFAULT 'ingredient',
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Payment Methods
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `currency` ENUM('USD','VES') NOT NULL,
  `type` ENUM('cash','bank','digital') NOT NULL DEFAULT 'cash',
  `is_active` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure payment methods exist for the new business eventually...
