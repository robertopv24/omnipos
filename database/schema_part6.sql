-- MariaDB 10.11+ Schema - Part 6: Audit & Authorizations

-- --------------------------------------------------------
-- Auditoría en Cuentas por Cobrar
-- --------------------------------------------------------
ALTER TABLE `accounts_receivable` 
ADD COLUMN IF NOT EXISTS `authorized_by` UUID DEFAULT NULL;

ALTER TABLE `accounts_receivable`
ADD FOREIGN KEY IF NOT EXISTS (`authorized_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- --------------------------------------------------------
-- Tabla de Logs de Autorización (Para auditoría general)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `authorization_logs` (
  `id` UUID NOT NULL DEFAULT UUID(),
  `business_id` UUID NOT NULL,
  `user_id` UUID NOT NULL COMMENT 'Cajero que solicitó',
  `supervisor_id` UUID NOT NULL COMMENT 'Supervisor que autorizó',
  `operation_type` VARCHAR(50) NOT NULL COMMENT 'Ej: credit_sale, discount, void',
  `reference_id` UUID DEFAULT NULL COMMENT 'ID de la orden o transacción',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`supervisor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
