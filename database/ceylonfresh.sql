-- Database Schema
CREATE DATABASE ceylonfresh;
USE ceylonfresh;

-- Users  
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_level ENUM('admin', 'customer') DEFAULT 'customer',
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories 
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products 
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Orders 
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Order items 
CREATE TABLE order_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert admin user (password_hash)
-- Note: Use PHP's password_hash('admin123', PASSWORD_DEFAULT) for the actual password
INSERT INTO users (username, email, password, user_level, full_name, phone) 
VALUES ('admin', 'admin@ceylonfresh.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', '+94771234567');

-- Insert categories (4 regions)
INSERT INTO categories (category_name, description) VALUES 
('Western Region', 'Colombo & coastal delicacies'),
('Northern Region', 'Jaffna specialties & Tamil cuisine'),
('Southern Region', 'Galle & Matara coastal flavors'),
('Central & Uva Region', 'Hill country & Kandyan cuisine');

-- Insert sample products for Western Region
INSERT INTO products (product_name, description, category_id, price, stock_quantity) VALUES 
('Kottu Roti', 'Chopped flatbread stir-fry', 1, 450.00, 50),
('Hoppers (Appa)', 'Plain, egg, and milk hoppers', 1, 250.00, 30),
('String Hoppers (Idiyappam)', 'Traditional string hoppers', 1, 300.00, 40),
('Lamprais', 'Dutch Burgher rice dish wrapped in banana leaf', 1, 650.00, 20),
('Fish Ambul Thiyal', 'Sour fish curry', 1, 350.00, 25),
('Cutlets & Patties', 'Street food snacks', 1, 200.00, 60),
('Pol Sambol', 'Coconut sambol', 1, 150.00, 80);

-- Insert sample products for Northern Region
INSERT INTO products (product_name, description, category_id, price, stock_quantity) VALUES 
('Jaffna Crab Curry', 'Authentic Jaffna crab curry', 2, 900.00, 15),
('Odiyal Kool', 'Seafood porridge', 2, 650.00, 20),
('Nandu Kulambu', 'Traditional crab curry', 2, 750.00, 18),
('Pittu', 'Steamed coconut & rice flour cylinders', 2, 250.00, 45),
('Vadai', 'Paruppu vadai, ulundu vadai', 2, 180.00, 70),
('Jaffna Mutton Curry', 'Spicy Jaffna mutton curry', 2, 850.00, 22),
('Thosai & Idli', 'South Indian influence', 2, 220.00, 50);

-- Insert sample products for Southern Region
INSERT INTO products (product_name, description, category_id, price, stock_quantity) VALUES 
('Fish Ambul Thiyal (Southern Style)', 'Southern style sour fish curry', 3, 380.00, 25),
('Maalu Baduma', 'Fried fish curry', 3, 380.00, 30),
('Kiribath with Lunu Miris', 'Milk rice with chili sambol', 3, 120.00, 40),
('Coconut Roti with Lunu Miris', 'Traditional coconut roti', 3, 150.00, 50),
('Polos Curry', 'Young jackfruit curry', 3, 130.00, 35),
('Kalu Dodol', 'Sweet dessert from Hambantota/Kalutara', 3, 90.00, 45),
('Halmilla Fish Curry', 'Traditional fish curry', 3, 320.00, 28);

-- Insert sample products for Central & Uva Region
INSERT INTO products (product_name, description, category_id, price, stock_quantity) VALUES 
('Kandyan Rice & Curry', 'Traditional Kandyan style', 4, 400.00, 30),
('Milk Rice with Honey', 'Sweet milk rice', 4, 250.00, 40),
('Kos Ata Curry', 'Jackfruit seeds curry', 4, 190.00, 35),
('Bath Curry', 'Red rice with chicken/mutton curries', 4, 350.00, 25),
('Thalaguli', 'Sesame & jaggery sweet', 4, 180.00, 60),
('Kevum & Kokis', 'Traditional sweets', 4, 120.00, 50),
('Herbal Porridge (Kola Kenda)', 'Traditional herbal porridge', 4, 150.00, 45);