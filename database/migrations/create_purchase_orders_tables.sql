-- Migración: Módulo de Compras/Proveedores
-- Fecha: 2025-12-18
-- Descripción: Crea tablas para gestión de órdenes de compra y recepción de mercancía
-- Tabla: purchase_orders
CREATE TABLE IF NOT EXISTS purchase_orders (
    id UUID PRIMARY KEY DEFAULT (UUID()),
    business_id UUID NOT NULL,
    supplier_id UUID NOT NULL,
    order_number VARCHAR(50) UNIQUE,
    total_cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'partial', 'received', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_by UUID NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    received_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_business_status (business_id, status),
    INDEX idx_supplier (supplier_id),
    INDEX idx_created_at (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabla: purchase_order_items
CREATE TABLE IF NOT EXISTS purchase_order_items (
    id UUID PRIMARY KEY DEFAULT (UUID()),
    purchase_order_id UUID NOT NULL,
    item_type ENUM('product', 'raw_material') NOT NULL,
    item_id UUID NOT NULL,
    quantity DECIMAL(20, 6) NOT NULL,
    unit_cost DECIMAL(10, 2) NOT NULL,
    received_quantity DECIMAL(20, 6) DEFAULT 0,
    batch_number VARCHAR(100),
    expiry_date DATE,
    notes TEXT,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    INDEX idx_purchase_order (purchase_order_id),
    INDEX idx_item (item_type, item_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Agregar columna is_active a suppliers si no existe
ALTER TABLE suppliers
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1
AFTER notes;
-- Crear índice en suppliers para búsquedas rápidas
CREATE INDEX IF NOT EXISTS idx_supplier_active ON suppliers(business_id, is_active);
-- Insertar método de pago "Transferencia" si no existe (útil para compras)
INSERT IGNORE INTO payment_methods (id, business_id, name, type, is_active)
SELECT UUID(),
    id,
    'Transferencia Bancaria',
    'bank_transfer',
    1
FROM businesses
WHERE NOT EXISTS (
        SELECT 1
        FROM payment_methods
        WHERE name = 'Transferencia Bancaria'
            AND business_id = businesses.id
    );