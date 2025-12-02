-- Create Database
CREATE DATABASE IF NOT EXISTS flavorful;
USE flavorful;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    country VARCHAR(255),
    address VARCHAR(255),
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    initials VARCHAR(2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    flavor VARCHAR(255),
    description TEXT,
    type ENUM('single', 'packet') DEFAULT 'single',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Messages Table (for contact form)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Balance/Account Table
CREATE TABLE IF NOT EXISTS account_balance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    balance DECIMAL(10, 2) DEFAULT 0,
    total_spent DECIMAL(10, 2) DEFAULT 0,
    total_orders INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Sample Products
INSERT INTO products (name, flavor, price, type, description) VALUES
('Pineapple', 'Pineapple', 0.50, 'single', 'Fresh pineapple flavored pennacool'),
('Strawberry', 'Strawberry', 0.50, 'single', 'Delicious strawberry flavored pennacool'),
('Grape', 'Grape', 0.50, 'single', 'Sweet grape flavored pennacool'),
('Cherry', 'Cherry', 0.50, 'single', 'Crisp cherry flavored pennacool'),
('Orange', 'Orange', 0.50, 'single', 'Citrus orange flavored pennacool'),
('Watermelon', 'Watermelon', 0.50, 'single', 'Refreshing watermelon flavored pennacool'),
('Mango', 'Mango', 0.50, 'single', 'Tropical mango flavored pennacool'),
('Lemon', 'Lemon', 0.50, 'single', 'Tangy lemon flavored pennacool'),
('Coconut', 'Coconut', 0.50, 'single', 'Exotic coconut flavored pennacool'),
('Cola', 'Cola', 0.50, 'single', 'Classic cola flavored pennacool'),
('Blue Raspberry', 'Blue Raspberry', 0.50, 'single', 'Fun blue raspberry flavored pennacool'),
('Small Packet', 'Mixed', 5.00, 'packet', 'Small packet with assorted flavors'),
('Large Packet', 'Mixed', 10.00, 'packet', 'Large packet with assorted flavors');