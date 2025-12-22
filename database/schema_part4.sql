-- MariaDB 10.11+ Schema - Part 4: Advanced Financial System

-- --------------------------------------------------------
-- Accounts Receivable (Cuentas por Cobrar)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `accounts_receivable` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `order_id` UUID DEFAULT NULL,
  `client_id` UUID DEFAULT NULL,
  `user_id` UUID DEFAULT NULL COMMENT 'Si es empleado',
  `amount` DECIMAL(20,6) NOT NULL,
  `paid_amount` DECIMAL(20,6) DEFAULT 0.000000,
  `status` ENUM('pending','partial','paid','deducted') NOT NULL DEFAULT 'pending',
  `due_date` DATE DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Accounts Payable (Cuentas por Pagar)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `accounts_payable` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `supplier_id` UUID DEFAULT NULL,
  `purchase_order_id` UUID DEFAULT NULL,
  `amount` DECIMAL(20,6) NOT NULL,
  `paid_amount` DECIMAL(20,6) DEFAULT 0.000000,
  `status` ENUM('pending','partial','paid','canceled') NOT NULL DEFAULT 'pending',
  `due_date` DATE DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Payroll and Benefits (Nómina y Beneficios)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll_payments` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `user_id` UUID NOT NULL,
  `amount` DECIMAL(20,6) NOT NULL,
  `deductions_amount` DECIMAL(20,6) DEFAULT 0.000000,
  `payment_date` DATE NOT NULL,
  `payment_method_id` UUID DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_by` UUID NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Change / Vueltos (Gestión de Vueltos Pendientes)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pending_changes` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `client_id` UUID NOT NULL,
  `amount` DECIMAL(20,6) NOT NULL,
  `currency` ENUM('USD','VES') NOT NULL,
  `status` ENUM('pending','used') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
