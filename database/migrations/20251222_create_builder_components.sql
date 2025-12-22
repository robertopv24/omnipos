-- Builder Components Table
CREATE TABLE IF NOT EXISTS builder_components (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-cube',
    category VARCHAR(100) DEFAULT 'Mis Bloques',
    tag_name VARCHAR(50) DEFAULT 'div',
    html_content LONGTEXT,
    styles_json TEXT,
    attributes_json TEXT,
    is_container BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);