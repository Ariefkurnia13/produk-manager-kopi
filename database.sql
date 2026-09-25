CREATE DATABASE IF NOT EXISTS product_manager
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE product_manager;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_product_price CHECK (price > 0),
    CONSTRAINT chk_product_stock CHECK (stock >= 0)
);

INSERT INTO products (name, category, price, stock) VALUES
('Kopi Gula Aren', 'Minuman', 15000, 25),
('Matcha Latte', 'Minuman', 18000, 18),
('Roti Cokelat', 'Makanan', 12000, 30)
ON DUPLICATE KEY UPDATE name = VALUES(name);
