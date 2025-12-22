-- Dynamic Pages Table (Visual Builder)
CREATE TABLE IF NOT EXISTS dynamic_pages (
    id CHAR(36) PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    layout_json LONGTEXT,
    access_level ENUM('public', 'auth', 'admin', 'super_admin') DEFAULT 'auth',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- custom_route_mappings (For Orphan Views binding)
CREATE TABLE IF NOT EXISTS route_mappings (
    id CHAR(36) PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    target_path VARCHAR(255) NOT NULL,
    -- e.g., 'admin/audit_authorizations'
    controller_action VARCHAR(255) DEFAULT NULL,
    -- Optional override
    access_level ENUM('public', 'auth', 'admin', 'super_admin') DEFAULT 'auth',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Index for fast lookup
CREATE INDEX idx_dynamic_slug ON dynamic_pages(slug);
CREATE INDEX idx_mapping_slug ON route_mappings(slug);