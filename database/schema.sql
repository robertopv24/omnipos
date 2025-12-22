-- MariaDB 10.11+ Schema
-- Optimized for native UUID type
-- Corrected Table Order for Foreign Keys

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `omnipos_saas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounts`
--
CREATE TABLE `accounts` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `company_name` VARCHAR(255) NOT NULL,
  `billing_email` VARCHAR(255) UNIQUE NOT NULL,
  `subscription_plan` ENUM('basic', 'pro', 'enterprise') NOT NULL DEFAULT 'basic',
  `status` ENUM('active', 'suspended', 'trialing') NOT NULL DEFAULT 'trialing',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `businesses`
--
CREATE TABLE `businesses` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `account_id` UUID NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `address` TEXT,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
  `timezone` VARCHAR(50) NOT NULL DEFAULT 'UTC',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_account_id` (`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--
CREATE TABLE `permissions` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `name` VARCHAR(100) NOT NULL, 
  `description` VARCHAR(255) DEFAULT NULL,
  `resource` VARCHAR(50) NOT NULL, 
  `action` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_permission` (`resource`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--
INSERT INTO `permissions` (`id`, `name`, `description`, `resource`, `action`) VALUES
('550e8400-e29b-41d4-a716-446655440000', 'view_dashboard', 'Ver el panel principal', 'dashboard', 'read'),
('550e8400-e29b-41d4-a716-446655440001', 'manage_products', 'Gestionar productos (CRUD)', 'products', 'manage'),
('550e8400-e29b-41d4-a716-446655440002', 'view_sales', 'Ver ventas y POS', 'sales', 'read'),
('550e8400-e29b-41d4-a716-446655440003', 'process_sales', 'Procesar ventas en el POS', 'sales', 'create'),
('550e8400-e29b-41d4-a716-446655440004', 'manage_inventory', 'Gestionar inventario y compras', 'inventory', 'manage'),
('550e8400-e29b-41d4-a716-446655440005', 'view_reports', 'Ver reportes de ventas y productos', 'reports', 'read'),
('550e8400-e29b-41d4-a716-446655440006', 'view_financial_reports', 'Ver reportes financieros (caja, transacciones)', 'financial_reports', 'read'),
('550e8400-e29b-41d4-a716-446655440007', 'manage_users', 'Gestionar usuarios de sus negocios', 'users', 'manage'),
('550e8400-e29b-41d4-a716-446655440008', 'manage_businesses', 'Crear, editar, eliminar sus propios negocios', 'businesses', 'manage'),
('550e8400-e29b-41d4-a716-446655440009', 'manage_account_settings', 'Acceder a la configuración de su cuenta', 'account_settings', 'read');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--
CREATE TABLE `roles` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `account_id` UUID NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_system_role` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_account_id` (`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--
CREATE TABLE `role_permissions` (
  `role_id` UUID NOT NULL,
  `permission_id` UUID NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  KEY `fk_role_permissions_permission` (`permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--
CREATE TABLE `users` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `document_id` VARCHAR(50) NOT NULL,
  `address` TEXT NOT NULL,
  `role` ENUM('super_admin', 'account_admin', 'business_admin', 'user') NOT NULL DEFAULT 'user',
  `account_id` UUID NULL,
  `business_id` UUID NULL, 
  `profile_pic` VARCHAR(255) DEFAULT 'default.jpg',
  `balance` DECIMAL(10,2) DEFAULT 0.00,
  `salary_amount` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
  `salary_frequency` ENUM('weekly','biweekly','monthly') NOT NULL DEFAULT 'monthly',
  `job_role` ENUM('manager','kitchen','cashier','delivery','other') NOT NULL DEFAULT 'other',
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `token_expiry` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `document_id` (`document_id`),
  KEY `idx_account_id` (`account_id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--
CREATE TABLE `user_roles` (
  `user_id` UUID NOT NULL,
  `role_id` UUID NOT NULL,
  `business_id` UUID NULL,
  PRIMARY KEY (`user_id`, `role_id`, `business_id`),
  KEY `fk_user_roles_role` (`role_id`),
  KEY `fk_user_roles_business` (`business_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `global_config`
--
CREATE TABLE `global_config` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `account_id` UUID NULL,
  `business_id` UUID NULL,
  `config_group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `config_key` VARCHAR(100) NOT NULL,
  `config_value` TEXT DEFAULT NULL,
  `value_type` ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
  `description` TEXT DEFAULT NULL,
  `is_public` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_config` (`account_id`, `business_id`, `config_key`),
  KEY `idx_account_id` (`account_id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--
CREATE TABLE `menus` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `account_id` UUID NULL,
  `parent_id` UUID NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(100) DEFAULT NULL,
  `position` INT(11) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `visibility` ENUM('public','private','admin','authenticated') NOT NULL DEFAULT 'public',
  `type` ENUM('header','sidebar','footer','mobile') NOT NULL DEFAULT 'sidebar',
  `required_permission_id` UUID NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `fk_menus_account` (`account_id`),
  KEY `fk_menus_parent` (`parent_id`),
  KEY `fk_menus_permission` (`required_permission_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`required_permission_id`) REFERENCES `permissions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--
CREATE TABLE `clients` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `document_id` VARCHAR(50) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `credit_limit` DECIMAL(20,6) DEFAULT 0.000000,
  `current_debt` DECIMAL(20,6) DEFAULT 0.000000,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--
CREATE TABLE `products` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price_usd` DECIMAL(10,2) NOT NULL,
  `price_ves` DECIMAL(10,2) NOT NULL,
  `stock` DECIMAL(20,6) NOT NULL,
  `product_type` ENUM('simple','compound','prepared') DEFAULT 'simple',
  `kitchen_station` ENUM('pizza','kitchen','bar') DEFAULT 'kitchen',
  `image_url` VARCHAR(255) DEFAULT NULL,
  `profit_margin` DECIMAL(5,2) NOT NULL DEFAULT 20.00,
  `linked_manufactured_id` UUID DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  KEY `idx_business_type` (`business_id`, `product_type`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--
CREATE TABLE `orders` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `user_id` UUID NOT NULL,
  `client_id` UUID DEFAULT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending','paid','preparing','ready','delivered','cancelled') DEFAULT 'pending',
  `consumption_type` ENUM('dine_in','takeaway','delivery') DEFAULT 'dine_in',
  `shipping_address` TEXT NOT NULL,
  `shipping_method` VARCHAR(100) DEFAULT NULL,
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_business_status_date` (`business_id`, `status`, `created_at`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cash_sessions` - MOVED UP
--
CREATE TABLE `cash_sessions` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `user_id` UUID NOT NULL,
  `opening_balance_usd` DECIMAL(10,2) DEFAULT 0.00,
  `opening_balance_ves` DECIMAL(10,2) DEFAULT 0.00,
  `closing_balance_usd` DECIMAL(10,2) DEFAULT 0.00,
  `closing_balance_ves` DECIMAL(10,2) DEFAULT 0.00,
  `calculated_usd` DECIMAL(10,2) DEFAULT 0.00,
  `calculated_ves` DECIMAL(10,2) DEFAULT 0.00,
  `status` ENUM('open','closed') DEFAULT 'open',
  `opened_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `closed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  KEY `idx_user_status` (`user_id`, `status`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transactions`
--
CREATE TABLE `transactions` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `cash_session_id` UUID NOT NULL,
  `type` ENUM('income','expense') NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `currency` ENUM('USD','VES') NOT NULL,
  `exchange_rate` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `amount_usd_ref` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `payment_method_id` UUID NOT NULL,
  `reference_type` ENUM('order','purchase','adjustment','manual','debt_payment') NOT NULL DEFAULT 'manual',
  `reference_id` UUID DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `created_by` UUID NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_business_id` (`business_id`),
  KEY `idx_cash_session_id` (`cash_session_id`),
  KEY `idx_reference` (`reference_type`, `reference_id`),
  KEY `idx_created_by` (`created_by`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cash_session_id`) REFERENCES `cash_sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_vault`
--
CREATE TABLE `company_vault` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL UNIQUE, 
  `balance_usd` DECIMAL(12,2) DEFAULT 0.00,
  `balance_ves` DECIMAL(12,2) DEFAULT 0.00,
  `last_updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `fk_company_vault_business` (`business_id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
